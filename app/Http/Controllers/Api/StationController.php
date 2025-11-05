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
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        // [تم التعديل هنا]
        // تحميل علاقتي الشركة والمنطقة لتجنب مشكلة N+1
        $stations = Station::with(['company', 'region'])->latest()->paginate(15);

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
            'data' => StationResource::make($station->load('company')),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
     public function show(Station $station): JsonResponse
    {
        return response()->json(StationResource::make($station->load('company')));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(UpdateStationRequest $request, Station $station): JsonResponse
    {
        $station->update($request->validated());
        return response()->json([
            'message' => 'Station updated successfully.',
            'data' => StationResource::make($station->fresh()->load('company')),
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
