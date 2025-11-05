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
     * @return AnonymousResourceCollection // <-- تعديل
     */
    public function index(): AnonymousResourceCollection // <-- تعديل
    {
        // eager load the driver relationship
        $trucks = Truck::with('driver')->latest()->paginate(15);
        return TruckResource::collection($trucks); // <-- تعديل
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
