<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Truck\StoreTruckRequest;
use App\Http\Requests\Truck\UpdateTruckRequest;
use App\Models\Truck;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\Api\TruckResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
// [الإصلاح الحاسم هنا] تصحيح مسار استيراد كلاس Request
use Illuminate\Http\Request;

class TruckController extends Controller
{
    use AuthorizesRequests;

    /**
     * Constructor to apply middleware.
     */
    public function __construct()
    {
        $this->authorizeResource(Truck::class, 'truck');
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // ابدأ ببناء الاستعلام مع تحميل علاقة السائق
        $query = Truck::with('driver');

        // تطبيق الفلاتر بشكل ديناميكي
        // الفلترة برقم الشاحنة
        $query->when($request->filled('truck_number'), function ($q) use ($request) {
            return $q->where('truck_number', 'like', '%' . $request->input('truck_number') . '%');
        });

        // الفلترة برقم المقطورة
        $query->when($request->filled('trailer_number'), function ($q) use ($request) {
            return $q->where('trailer_number', 'like', '%' . $request->input('trailer_number') . '%');
        });

        // الفلترة بمعرّف السائق
        $query->when($request->filled('driver_id'), function ($q) use ($request) {
            return $q->where('driver_id', $request->input('driver_id'));
        });

        // ترتيب النتائج حسب الأحدث وتقسيمها إلى صفحات مع الحفاظ على الفلاتر
        $trucks = $query->latest()->paginate(15)->withQueryString();

        return TruckResource::collection($trucks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTruckRequest $request): JsonResponse
    {
        $truck = Truck::create($request->validated());
        return response()->json([
            'message' => 'Truck created successfully.',
            'data' => TruckResource::make($truck->load('driver')),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Truck $truck): JsonResponse
    {
        return response()->json(TruckResource::make($truck->load('driver')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTruckRequest $request, Truck $truck): JsonResponse
    {
        $truck->update($request->validated());
        return response()->json([
            'message' => 'Truck updated successfully.',
            'data' => TruckResource::make($truck->fresh()->load('driver')),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Truck $truck): JsonResponse
    {
        $truck->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
