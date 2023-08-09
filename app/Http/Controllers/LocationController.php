<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationAverageRequest;
use App\Http\Requests\LocationStoreRequest;
use App\Http\Resources\LocationCollection;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Repositories\WeatherDataRepository;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    private WeatherDataRepository $weatherDataRepository;

    public function __construct(WeatherDataRepository $weatherDataRepository)
    {
        $this->weatherDataRepository = $weatherDataRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new LocationCollection(Location::paginate(5));
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

    public function averageWeather(LocationAverageRequest $request, int $id)
    {
        $location = Location::findOrFail($id);
        $post = $request->validated();
        $collection = [
            'start_date'  => $post['start_date'],
            'finish_date' => $post['finish_date'],
            'values'      => $this->weatherDataRepository->getAverageData($location, $post)
        ];

        return $collection;
    }
}
