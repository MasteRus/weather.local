<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationAverageRequest;
use App\Http\Requests\LocationStoreRequest;
use App\Http\Resources\LocationCollection;
use App\Http\Resources\LocationResource;
use App\Http\Responses\LocationAverageWeatherResponse;
use App\Models\Location;
use App\Repositories\ILocationRepository;
use App\Repositories\IWeatherDataRepository;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Location",
 *     description="API Endpoints of Locations"
 * )
 */
class LocationController extends Controller
{
    private IWeatherDataRepository $weatherDataRepository;
    private ILocationRepository $locationRepository;

    public function __construct(
        IWeatherDataRepository $weatherDataRepository,
        ILocationRepository $locationRepository
    ) {
        $this->weatherDataRepository = $weatherDataRepository;
        $this->locationRepository = $locationRepository;
    }

    /**
     * @OA\Get(
     *     path="/locations",
     *     summary="List",
     *     tags={"Location"},
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/LocationCollection")
     *      )
     * )
     */
    public function index(): LocationCollection
    {
        return new LocationCollection($this->locationRepository->paginate(20));
    }

    /**
     * @OA\Post(
     *     path="/locations",
     *     summary="Create location",
     *     tags={"Location"},
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="latitude",
     *                      type="number",
     *                  ),
     *                  @OA\Property(
     *                       property="longitude",
     *                       type="number",
     *                   ),
     *                  example={"name": "Washington", "latitude": 47.751076, "longitude": -120.740135}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *           response=200,
     *           description="OK",
     *           @OA\Schema(
     *               schema="LocationResponse",
     *               type="object",
     *               @OA\Property(
     *                   property="data",
     *                   type="array",
     *                   @OA\Items(ref="#/components/schemas/Location")
     *               )
     *           )
     *       )
     * )
     */
    public function store(LocationStoreRequest $request): LocationResource
    {
        $location = $this->locationRepository->create($request->validated());

        return new LocationResource($location);
    }

    /**
     * @OA\Get(
     *     path="/locations/{id}",
     *     summary="Show",
     *     tags={"Location"},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="The id location",
     *          @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\Schema(
     *              schema="LocationResponse",
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Location")
     *              )
     *          )
     *      )
     * )
     */
    public function show(int $id): LocationResource
    {
        return new LocationResource($this->locationRepository->getById($id));
    }

    /**
     * @OA\Patch(
     *     path="/locations/{id}",
     *     summary="Update location",
     *     tags={"Location"},
     *     @OA\Parameter(
     *           name="id",
     *           in="path",
     *           required=true,
     *           description="The id location",
     *           @OA\Schema(
     *               type="number"
     *           )
     *     ),
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="latitude",
     *                      type="number",
     *                  ),
     *                  @OA\Property(
     *                       property="longitude",
     *                       type="number",
     *                   ),
     *                  example={"name": "Washington", "latitude": 47.751076, "longitude": -120.740135}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *           response=200,
     *           description="OK",
     *           @OA\Schema(
     *               schema="LocationResponse",
     *               type="object",
     *               @OA\Property(
     *                   property="data",
     *                   type="array",
     *                   @OA\Items(ref="#/components/schemas/Location")
     *               )
     *           )
     *       )
     * )
     */
    public function update(LocationStoreRequest $request, int $id): LocationResource
    {
        $location = $this->locationRepository->update($id, $request->validated());

        return new LocationResource($location);
    }

    /**
     * @OA\Delete(
     *     path="/locations/{id}",
     *     summary="Delete",
     *     tags={"Location"},
     *     @OA\Parameter(
     *           name="id",
     *           in="path",
     *           required=true,
     *           description="The id location",
     *           @OA\Schema(
     *               type="number"
     *           )
     *     ),
     *     @OA\Response(
     *            response=200,
     *            description="OK",
     *            @OA\Schema(
     *                schema="LocationResponse",
     *                type="object",
     *                @OA\Property(
     *                    property="data",
     *                    type="array",
     *                    @OA\Items(ref="#/components/schemas/Location")
     *                )
     *            )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->locationRepository->delete($id);

        return response()->json(null);
    }

    /**
     * @OA\Post(
     *     path="/locations/{id}/average-weather",
     *     summary="Delete",
     *     tags={"Location"},
     *     @OA\Parameter(
     *            name="id",
     *            in="path",
     *            required=true,
     *            description="The id location",
     *            @OA\Schema(
     *                type="number"
     *            )
     *     ),
     *     @OA\Response(
     *            response=200,
     *            description="OK",
     *            @OA\Schema(
     *                schema="LocationResponse",
     *                type="object",
     *                @OA\Property(
     *                    property="data",
     *                    type="array",
     *                    @OA\Items(ref="#/components/schemas/Location")
     *                )
     *            )
     *      )
     * )
     */
    public function averageWeather(LocationAverageRequest $request, int $id): JsonResponse
    {
        $location = Location::findOrFail($id);
        $post = $request->validated();

        $response = new LocationAverageWeatherResponse(
            $post['start_date'],
            $post['finish_date'],
            $this->weatherDataRepository->getAverageData($location, $post)->all()
        );

        return response()->json($response);
    }
}
