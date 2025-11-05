<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FuelOrder\StoreFuelOrderRequest;
use App\Http\Requests\FuelOrder\UpdateFuelOrderRequest;
use App\Models\FuelOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\Api\FuelOrderResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FuelOrderController extends Controller
{
       /**
     * Display a listing of the resource with advanced filtering.
     * Can return paginated or all results based on 'printable' parameter.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 1. التحقق من الصلاحية (لا تغيير)
        $this->authorize('viewAny', FuelOrder::class);

        // 2. بناء الاستعلام الأساسي (لا تغيير)
        $query = FuelOrder::with([
            'driver', 'product', 'status', 'station.company', 'station.region'
        ]);

        // 3. تطبيق الفلاتر (لا تغيير)
        $query->when($request->filled('driver_id'), fn($q) => $q->where('driver_id', $request->input('driver_id')));
        $query->when($request->filled('product_id'), fn($q) => $q->where('product_id', $request->input('product_id')));
        $query->when($request->filled('order_status_id'), fn($q) => $q->where('order_status_id', $request->input('order_status_id')));
        $query->when($request->filled('station_id'), fn($q) => $q->where('station_id', $request->input('station_id')));
        $query->when($request->filled('region_id'), fn($q) => $q->whereHas('station', fn($sq) => $sq->where('region_id', $request->input('region_id'))));
        $query->when($request->filled('company_id'), fn($q) => $q->whereHas('station', fn($sq) => $sq->where('company_id', $request->input('company_id'))));
        $query->when($request->filled('start_date'), fn($q) => $q->whereDate('order_date', '>=', Carbon::parse($request->input('start_date'))));
        $query->when($request->filled('end_date'), fn($q) => $q->whereDate('order_date', '<=', Carbon::parse($request->input('end_date'))));

        // --- [بداية التعديل الدقيق هنا] ---
        // 4. تحديد طريقة إرجاع النتائج
        if ($request->boolean('printable')) {
            // إذا كان الطلب للطباعة، أرجع كل النتائج بدون ترقيم صفحات
            $orders = $query->latest('order_date')->get();
            // في هذه الحالة، نرجع استجابة JSON مباشرة لأنها لا تحتوي على بيانات meta للترقيم
            return response()->json(['data' => FuelOrderResource::collection($orders)]);
        } else {
            // في الحالة العادية، أرجع النتائج مع ترقيم الصفحات
            $orders = $query->latest('order_date')->paginate(15)->withQueryString();
            // نرجع الـ Resource Collection مباشرة لأنه يعالج بيانات الترقيم
            return FuelOrderResource::collection($orders);
        }
        // --- [نهاية التعديل الدقيق هنا] ---
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFuelOrderRequest $request): JsonResponse
    {
        $this->authorize('create', FuelOrder::class);

        $fuelOrder = FuelOrder::create($request->validated());

        return response()->json([
            'message' => 'Fuel order created successfully.',
            'data' => FuelOrderResource::make($fuelOrder->load(['driver', 'station', 'product', 'status'])),
        ], Response::HTTP_CREATED);
    }

      /**
     * Display the specified resource.
     */
    public function show(FuelOrder $fuelOrder): JsonResponse
    {
        $this->authorize('view', $fuelOrder);

        // --- [بداية التعديل] ---
        // قم بتحميل كل العلاقات اللازمة، بما في ذلك العلاقات المتداخلة
        $fuelOrder->load([
            'driver',

            'product',
            'status',
            'station.company', // <-- هذا السطر مهم جدًا
            'station.region'   // <-- وهذا السطر مهم جدًا
        ]);
        // --- [نهاية التعديل] ---

        // أرجع الـ Resource بعد تحميل كل البيانات
        return response()->json(FuelOrderResource::make($fuelOrder));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFuelOrderRequest $request, FuelOrder $fuelOrder): JsonResponse
    {
        $this->authorize('update', $fuelOrder);

        $fuelOrder->update($request->validated());

        return response()->json([
            'message' => 'Fuel order updated successfully.',
            'data' => FuelOrderResource::make($fuelOrder->fresh()->load(['driver', 'station', 'product', 'status'])),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FuelOrder $fuelOrder): JsonResponse
    {
        $this->authorize('delete', $fuelOrder);

        $fuelOrder->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
