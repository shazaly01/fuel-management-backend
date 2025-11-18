<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Station\StoreStationRequest;
use App\Http\Requests\Station\UpdateStationRequest;
use App\Models\Station;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\Api\StationResource;
use Illuminate\Http\Request; // [إضافة] استيراد كلاس Request
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Constructor to apply middleware.
     */
    public function __construct()
    {
        $this->authorizeResource(Station::class, 'station');
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // ابدأ ببناء الاستعلام مع تحميل العلاقات
        $query = Station::with(['company', 'region']);

        // [تصحيح] استخدام filled() و input() لتطبيق الفلاتر بشكل صحيح
        // الفلترة بالاسم
        $query->when($request->filled('name'), function ($q) use ($request) {
            return $q->where('name', 'like', '%' . $request->input('name') . '%');
        });

        // الفلترة برقم المحطة
        $query->when($request->filled('station_number'), function ($q) use ($request) {
            return $q->where('station_number', 'like', '%' . $request->input('station_number') . '%');
        });

        // الفلترة بمعرّف الشركة
        $query->when($request->filled('company_id'), function ($q) use ($request) {
            return $q->where('company_id', $request->input('company_id'));
        });

        // الفلترة بمعرّف المنطقة
        $query->when($request->filled('region_id'), function ($q) use ($request) {
            return $q->where('region_id', $request->input('region_id'));
        });

        // [تصحيح] استخدام withQueryString() لترقيم الصفحات
        $stations = $query->latest()->paginate(15)->withQueryString();

        return StationResource::collection($stations);
    }


    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreStationRequest $request): JsonResponse
    {
        $station = Station::create($request->validated());
        return response()->json([
            'message' => 'Station created successfully.',
            'data' => StationResource::make($station->load('company', 'region')), // تم إضافة region هنا أيضاً
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
     public function show(Station $station): JsonResponse
    {
        // تم إضافة region هنا أيضاً
        return response()->json(StationResource::make($station->load('company', 'region')));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(UpdateStationRequest $request, Station $station): JsonResponse
    {
        $station->update($request->validated());
        return response()->json([
            'message' => 'Station updated successfully.',
            // تم إضافة region هنا أيضاً
            'data' => StationResource::make($station->fresh()->load('company', 'region')),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Station $station): JsonResponse
    {
        $station->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
