<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     *
     * @OA\Schema(
     *  schema="Location",
     *  title="Location for weather data",
     * 	@OA\Property(
     *        property="id",
     *        type="number"
     *    ),
     * 	@OA\Property(
     *        property="name",
     *        type="string"
     *    ),
     *  @OA\Property(
     *        property="latitude",
     *        type="number"
     *  ),
     *  @OA\Property(
     *        property="longitude",
     *        type="number"
     *   ),
     *  @OA\Property(
     *        property="latitude_display",
     *        type="string"
     *    ),
     *  @OA\Property(
     *        property="longitude_display",
     *        type="string"
     *    ),
     *  @OA\Property(
     *         property="created_at",
     *         type="string"
     *     ),
     *  @OA\Property(
     *         property="updated_at",
     *         type="string"
     *     ),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'latitude'  => (float)$this->latitude,
            'longitude' => (float)$this->longitude,

            'latitude_display'  => number_format($this->latitude, 8),
            'longitude_display' => number_format($this->longitude, 8),

            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}
