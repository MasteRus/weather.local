<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationStoreRequest;
use App\Http\Resources\LocationCollection;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new LocationCollection(Location::paginate(3));
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Throwable
     */
    public function store(LocationStoreRequest $request)
    {
        $post = $request->validated();
        $location = new Location($post);
        $location->saveOrFail();

        return new LocationResource($location);
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location): LocationResource
    {
        return new LocationResource($location);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationStoreRequest $request, Location $location): LocationResource
    {
        $post = $request->validated();
        $location->update($post);

        return new LocationResource($location);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location): JsonResponse
    {
        $location->delete();

        return response()->json(null, 204);
    }
}
