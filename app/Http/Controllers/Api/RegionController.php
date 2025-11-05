<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Region\StoreRegionRequest;
use App\Http\Requests\Region\UpdateRegionRequest;
use App\Http\Resources\Api\RegionResource;
use App\Models\Region;
use Illuminate\Http\Response;

class RegionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Region::class, 'region');
    }

    public function index()
    {
        $regions = Region::latest()->paginate(15);
        return RegionResource::collection($regions);
    }

    public function store(StoreRegionRequest $request)
    {
        $region = Region::create($request->validated());
        return new RegionResource($region);
    }

    public function show(Region $region)
    {
        return new RegionResource($region);
    }

    public function update(UpdateRegionRequest $request, Region $region)
    {
        $region->update($request->validated());
        return new RegionResource($region->fresh());
    }

    public function destroy(Region $region)
    {
        $region->delete();
        return response()->noContent();
    }
}
