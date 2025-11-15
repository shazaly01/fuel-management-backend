<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkNature\StoreWorkNatureRequest;
use App\Http\Requests\WorkNature\UpdateWorkNatureRequest;
use App\Http\Resources\Api\WorkNatureResource;
use App\Models\WorkNature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class WorkNatureController extends Controller
{
    /**
     * Constructor to apply middleware.
     */
    public function __construct()
    {
        $this->authorizeResource(WorkNature::class, 'work_nature');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $workNatures = WorkNature::latest()->get();
        return WorkNatureResource::collection($workNatures);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkNatureRequest $request): JsonResponse
    {
        $workNature = WorkNature::create($request->validated());

        return response()->json([
            'message' => 'Work Nature created successfully.',
            'data' => WorkNatureResource::make($workNature),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkNature $workNature): WorkNatureResource
    {
        return WorkNatureResource::make($workNature);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkNatureRequest $request, WorkNature $workNature): JsonResponse
    {
        $workNature->update($request->validated());

        return response()->json([
            'message' => 'Work Nature updated successfully.',
            'data' => WorkNatureResource::make($workNature->fresh()),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkNature $workNature): Response
    {
        $workNature->delete();

        return response()->noContent();
    }
}
