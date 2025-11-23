<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\DriverResource;
use App\Models\Driver;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use App\Models\FuelOrder;
use App\Http\Resources\Api\FuelOrderResource;
use App\Models\Station;
use App\Http\Resources\Api\StationResource;
use App\Models\Setting;
use Illuminate\Support\Facades\DB; // <-- إضافة ضرورية لاستخدام DB::raw
use App\Models\Company;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\Api\DashboardStatsResource;

class ReportController extends Controller
{

   /**
     * Generate a complete report for drivers with advanced filtering.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function driverReport(Request $request): AnonymousResourceCollection
    {
        // 1. التحقق من الصلاحية
        $this->authorize('viewAny', Driver::class);

        // 2. بناء الاستعلام الأساسي مع تحميل علاقة الشاحنة
        $query = Driver::with('truck');

        // 3. تطبيق الفلاتر المتقدمة
        // فلتر: الحالة (status)
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // فلتر: سقف الطلبيات
        if ($request->filled('order_limit')) {
            $limit = (int) $request->input('order_limit');
            $deliveredStatus = OrderStatus::where('name', 'Delivered')->first();

            if ($deliveredStatus) {
                $subquery = FuelOrder::select('driver_id')
                    ->where('order_status_id', '!=', $deliveredStatus->id)
                    ->groupBy('driver_id')
                    ->having(DB::raw('COUNT(id)'), '>=', $limit);

                $query->whereNotIn('id', $subquery);
            }
        }

        // 4. جلب النتائج وترتيبها مع إضافة ترقيم الصفحات
        // --- [تم التعديل هنا] ---
        // احصل على عدد العناصر المطلوبة لكل صفحة من الطلب، مع قيمة افتراضية 15
        $perPage = $request->input('per_page', 15);

        // استخدم paginate() بدلاً من get(). إذا كانت per_page = -1، جلب كل النتائج.
        $drivers = $query->latest()->paginate($perPage == -1 ? $query->count() : $perPage);
        // --- [نهاية التعديل] ---

        return DriverResource::collection($drivers);
    }


    /**
     * Generate a comprehensive report for fuel orders with advanced filtering.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function orderReport(Request $request): AnonymousResourceCollection
    {
        // 1. التحقق من الصلاحية
        $this->authorize('viewAny', FuelOrder::class);

        // 2. بناء الاستعلام الأساسي مع تحميل جميع العلاقات اللازمة لتحسين الأداء
        // قمنا بإضافة station.company و station.region للفلترة والعرض
        $query = FuelOrder::with([
            'driver',
            'station.company', // تحميل الشركة من خلال المحطة
            'station.region',  // تحميل المنطقة من خلال المحطة
            'product',
            'status'
        ]);

        // 3. تطبيق الفلاتر بشكل ديناميكي ومنظم باستخدام ->when()
        // when() هي أفضل ممارسة لتطبيق الفلاتر بشكل شرطي، مما يجعل الكود نظيفًا ومقروءًا.

        // --- الفلاتر المباشرة (على جدول fuel_orders) ---
        $query->when($request->filled('order_status_id'), function ($q) use ($request) {
            $q->where('order_status_id', $request->input('order_status_id'));
        });

        $query->when($request->filled('driver_id'), function ($q) use ($request) {
            $q->where('driver_id', $request->input('driver_id'));
        });

        $query->when($request->filled('station_id'), function ($q) use ($request) {
            $q->where('station_id', $request->input('station_id'));
        });

        $query->when($request->filled('product_id'), function ($q) use ($request) {
            $q->where('product_id', $request->input('product_id'));
        });

        // --- الفلاتر المتداخلة (عبر علاقة المحطة) ---
        // whereHas() هي أفضل ممارسة للفلترة بناءً على وجود علاقة تستوفي شروطًا معينة.

        // فلتر حسب الشركة
        $query->when($request->filled('company_id'), function ($q) use ($request) {
            $q->whereHas('station', function ($stationQuery) use ($request) {
                $stationQuery->where('company_id', $request->input('company_id'));
            });
        });

        // فلتر حسب المنطقة
        $query->when($request->filled('region_id'), function ($q) use ($request) {
            $q->whereHas('station', function ($stationQuery) use ($request) {
                $stationQuery->where('region_id', $request->input('region_id'));
            });
        });

        // --- فلاتر التواريخ (يمكن تحسينها لتدعم نطاقًا) ---
        $query->when($request->filled('start_date'), function ($q) use ($request) {
            $q->where('order_date', '>=', Carbon::parse($request->input('start_date')));
        });

        $query->when($request->filled('end_date'), function ($q) use ($request) {
            $q->where('order_date', '<=', Carbon::parse($request->input('end_date')));
        });


        // 4. إرجاع النتائج مع الترتيب والترقيم
        // withQueryString() تضمن أن تبقى الفلاتر في روابط الترقيم
        $orders = $query->latest('order_date')->paginate(15)->withQueryString();

        return FuelOrderResource::collection($orders);
    }
     /**
     * Generate a report for stations with advanced filtering.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function stationReport(Request $request): AnonymousResourceCollection
    {
        // 1. التحقق من الصلاحية
        $this->authorize('viewAny', Station::class);

        // 2. بناء الاستعلام الأساسي مع تحميل علاقة الشركة (Eager Loading)
        $query = Station::with('company');

        // 3. تطبيق الفلاتر
        // فلتر: الشركة (company_id)
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->input('company_id'));
        }

        // 4. إرجاع النتائج
        $stations = $query->latest()->paginate(15)->withQueryString();

        return StationResource::collection($stations);
    }




   /**
 * Get aggregated statistics for the main dashboard.
 *
 * @return DashboardStatsResource
 */
public function dashboardStats(): DashboardStatsResource
{
    $this->authorize('dashboard.view');

    $stats = Cache::remember('dashboard_stats', now()->addMinutes(5), function () {

        // --- [بداية الإصلاح هنا] ---
        $orderStats = OrderStatus::withCount('fuelOrders')
            ->get()
            ->map(function ($status) {
                return [
                    'id' => $status->id,
                    'name' => $status->name,
                    'color' => $status->color, // <-- السطر الحاسم: إضافة حقل اللون
                    'count' => $status->fuel_orders_count,
                ];
            });
        // --- [نهاية الإصلاح هنا] ---

        $driverStats = Driver::query()
            ->selectRaw("
                COUNT(id) as total,
                SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available,
                SUM(CASE WHEN status = 'on_trip' THEN 1 ELSE 0 END) as on_trip
            ")
            ->first();

        $generalCounts = [
            'users' => \App\Models\User::count(),
            'companies' => \App\Models\Company::count(),
            'stations' => \App\Models\Station::count(),
            'trucks' => \App\Models\Truck::count(),
        ];

        return [
            'general' => $generalCounts,
            'drivers' => [
                'total' => (int) $driverStats->total,
                'available' => (int) $driverStats->available,
                'on_trip' => (int) $driverStats->on_trip,
            ],
            'orders' => $orderStats,
        ];
    });

    return new DashboardStatsResource($stats);
}

    // ... (الدوال الحالية مثل dashboardStats, driverReport, etc.)

    /**
     * Generate a daily movement order report for a specific company.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function movementOrderReport(Request $request)
    {
        // 1. التحقق من صحة المدخلات
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        $reportDate = Carbon::parse($validated['date']);
        $companyId = $validated['company_id'];

        // 2. حساب "رقم أمر الحركة" الفريد
        // الصيغة: COMPANY_ID - YYMMDD
        $referenceNumber = sprintf('%d-%s', $companyId, $reportDate->format('ymd'));

        // 3. جلب بيانات الشركة
        $company = Company::findOrFail($companyId);

        // 4. جلب الطلبيات التي تطابق الشروط
        $orders = FuelOrder::query()
            ->with(['driver.truck', 'station', 'product']) // تحميل العلاقات اللازمة
            ->whereDate('order_date', $reportDate) // فلترة حسب التاريخ المحدد
            ->whereHas('station', function ($query) use ($companyId) {
                $query->where('company_id', $companyId); // فلترة حسب الشركة
            })
            ->orderBy('id', 'asc') // ترتيب الطلبيات تصاعديًا حسب رقمها التسلسلي في اليوم
            ->get();

        // 5. إرجاع كل البيانات في استجابة JSON واحدة
        return response()->json([
            'data' => [
                'report_date' => $reportDate->format('Y-m-d'),
                'reference_number' => $referenceNumber,
                'company' => [
                    'name' => $company->name,
                    // يمكنك إضافة أي بيانات أخرى عن الشركة هنا إذا احتجت
                ],
                'orders' => FuelOrderResource::collection($orders),
            ]
        ]);
    }
}
