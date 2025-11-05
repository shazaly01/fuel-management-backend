<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\StoreDriverRequest;
use App\Http\Requests\Driver\UpdateDriverRequest;
use App\Models\Driver;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\Api\DriverResource; // <-- أضف هذا
use App\Http\Resources\Api\TruckResource; // <-- أضف هذا (للعلاقة)
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DriverController extends Controller
{
    use AuthorizesRequests;

    /**
     * Constructor to apply middleware.
     */
    public function __construct()
    {
        // تطبيق صلاحيات الـ Policy على جميع دوال الـ Controller
        $this->authorizeResource(Driver::class, 'driver');
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     * @return AnonymousResourceCollection // <-- تعديل
     */
    public function index(): AnonymousResourceCollection // <-- تعديل
    {
        $drivers = Driver::query()->latest()->paginate(15);
        return DriverResource::collection($drivers); // <-- تعديل
    }


    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreDriverRequest $request): JsonResponse
    {
        $driver = Driver::create($request->validated());
        return response()->json([
            'message' => 'Driver created successfully.',
            'data' => DriverResource::make($driver),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
  public function show(Driver $driver): JsonResponse
    {
        return response()->json(DriverResource::make($driver->load('truck')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDriverRequest $request, Driver $driver): JsonResponse
    {
        $driver->update($request->validated());

        return response()->json([
            'message' => 'Driver updated successfully.',
            'data' => $driver->fresh(),
        ]);
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
