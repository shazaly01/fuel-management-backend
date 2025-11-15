<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\StoreDriverRequest;
use App\Http\Requests\Driver\UpdateDriverRequest;
use App\Models\Driver;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\Api\DriverResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    use AuthorizesRequests;

    /**
     * Constructor to apply middleware.
     */
    public function __construct()
    {
        $this->authorizeResource(Driver::class, 'driver');
    }

  /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection // <-- 2. حقن Request
    {
        // 3. بناء الاستعلام الأساسي مع العلاقات
        $query = Driver::query()
        ->with(['truck', 'workNature'])
        ->withCount('fuelOrders');

        // 4. تطبيق البحث (Search) إذا كان موجودًا
        $query->when($request->input('search'), function ($q, $searchTerm) {
            // يبحث في الاسم أو رقم الهاتف
            $q->where(function($subQuery) use ($searchTerm) {
                $subQuery->where('name', 'like', "%{$searchTerm}%")
                         ->orWhere('phone_number', 'like', "%{$searchTerm}%");
            });
        });

        // 5. تطبيق فلتر الحالة (Status) إذا كان موجودًا
        $query->when($request->input('status'), function ($q, $status) {
            $q->where('status', $status);
        });

        // 6. تطبيق فلتر طبيعة العمل (Work Nature) إذا كان موجودًا
        $query->when($request->input('work_nature_id'), function ($q, $workNatureId) {
            $q->where('work_nature_id', $workNatureId);
        });

        // 7. الترتيب والترقيم
        $drivers = $query->latest()->paginate(15)
                         ->withQueryString(); // <-- 8. إضافة بارامترات الفلترة إلى روابط الترقيم

        return DriverResource::collection($drivers);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDriverRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        // --- بداية الإضافة: معالجة رفع الصورة ---
        if ($request->hasFile('document_image')) {
            $path = $request->file('document_image')->store('driver_documents', 'public');
            $validatedData['document_image_path'] = $path;
        }
        // --- نهاية الإضافة ---

        $driver = Driver::create($validatedData);

        return response()->json([
            'message' => 'Driver created successfully.',
            'data' => DriverResource::make($driver->load(['truck', 'workNature'])),
        ], Response::HTTP_CREATED);
    }


    /**
     * Display the specified resource.
     */
    public function show(Driver $driver): JsonResponse
    {
        // تم التعديل: تحميل علاقة workNature
        return response()->json(DriverResource::make($driver->load(['truck', 'workNature'])));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDriverRequest $request, Driver $driver): JsonResponse
    {
        $validatedData = $request->validated();

        // --- بداية الإضافة: معالجة تحديث الصورة ---
        if ($request->hasFile('document_image')) {
            // 1. حذف الصورة القديمة إذا كانت موجودة
            if ($driver->document_image_path) {
                Storage::disk('public')->delete($driver->document_image_path);
            }
            // 2. تخزين الصورة الجديدة
            $path = $request->file('document_image')->store('driver_documents', 'public');
            $validatedData['document_image_path'] = $path;
        }
        // --- نهاية الإضافة ---

        $driver->update($validatedData);

        return response()->json([
            'message' => 'Driver updated successfully.',
            'data' => DriverResource::make($driver->fresh()->load(['truck', 'workNature'])),
        ]);
    }


      /**
     * Display a listing of the fuel orders for a specific driver.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getFuelOrders(Driver $driver)
    {
        // التحقق من الصلاحية (اختياري ولكنه موصى به)
        $this->authorize('viewAny', \App\Models\FuelOrder::class);

        // جلب آخر 10 طلبيات للسائق المحدد مع العلاقات اللازمة
        $orders = $driver->fuelOrders()
                         ->with(['station', 'product', 'status'])
                         ->latest()
                         ->paginate(10);

        // استخدام FuelOrderResource لضمان تناسق شكل البيانات مع بقية التطبيق
        return \App\Http\Resources\Api\FuelOrderResource::collection($orders);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver): JsonResponse
    {
        $driver->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
