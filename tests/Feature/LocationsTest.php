<?php

namespace Tests\Feature;

use App\Http\Resources\LocationCollection;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Models\Parameter;
use App\Models\WeatherData;
use App\Service\WeatherDataSource\OpenMeteoSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationsTest extends TestCase
{
    use RefreshDatabase;

    public const CREATE_REQUEST = [
        'name'      => 'Washingtonsk',
        'latitude'  => 47.751076,
        'longitude' => -120.740135,
    ];

    public const CREATE_BAD_REQUEST = [
        'name'      => '',
        'latitude'  => 1147.751076,
        'longitude' => -1120.740135,
    ];

    public const UPDATE_REQUEST = [
        'name'      => 'Washington',
        'latitude'  => 47.751075,
        'longitude' => -120.740134,
    ];

    public const UPDATE_BAD_REQUEST = [
        'name'      => '',
        'latitude'  => 2247.751075,
        'longitude' => -1220.740134,
    ];

    public const VALIDATION_ERRORS_RESPONSE = [
        'name'      => [
            'The name field is required.'
        ],
        'latitude'  => [
            'The latitude field must be between -90 and 90.'
        ],
        'longitude' => [
            'The longitude field must be between -180 and 180.'
        ]
    ];

    public function test_create_success(): void
    {
        $response = $this->postJson('/api/locations', self::CREATE_REQUEST);
        $location = Location::first();

        $this->assertCount(1, Location::all());
        $response->assertStatus(201);
        $response->assertJson((new LocationResource($location))->response()->getData(true));
    }

    public function test_create_validation_errors(): void
    {
        $response = $this->postJson('/api/locations', self::CREATE_BAD_REQUEST);

        $this->assertCount(0, Location::all());
        $response->assertStatus(422);
        $response->assertJson(self::VALIDATION_ERRORS_RESPONSE);
    }

    public function test_update_success(): void
    {
        $location = Location::factory()->create();
        $response = $this->patchJson('/api/locations/' . $location->id, self::UPDATE_REQUEST);
        $location = Location::first();

        $this->assertCount(1, Location::all());
        $response->assertStatus(200);
        $response->assertJson((new LocationResource($location))->response()->getData(true));
    }

    public function test_validation_errors(): void
    {
        $location = Location::factory()->create();
        $response = $this->patchJson('/api/locations/' . $location->id, self::UPDATE_BAD_REQUEST);

        $response->assertStatus(422);
        $response->assertJson(self::VALIDATION_ERRORS_RESPONSE);
    }

    public function test_location_not_found(): void
    {
        $response = $this->patchJson('/api/locations/' . 99999, self::UPDATE_REQUEST);

        $response->assertStatus(404);
    }

    public function test_locations_index(): void
    {
        $locations = Location::factory()->count(5)->create();
        $response = $this->getJson('/api/locations');
        $response->assertStatus(200);
        $response->assertJson((new LocationCollection($locations))->response()->getData(true));
    }

    public function test_location_show(): void
    {
        $location = Location::factory()->create();
        $response = $this->getJson('/api/locations/' . $location->id);
        $response->assertStatus(200);
        $response->assertJson((new LocationResource($location))->response()->getData(true));
    }

    public function test_location_delete(): void
    {
        $location = Location::factory()->create();
        $response = $this->deleteJson('/api/locations/' . $location->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }

    public function test_location_average_success(): void
    {
        $post = [
            'start_date'  => '2020-01-01 00:00:00',
            'finish_date' => '2020-01-02 23:59:59'
        ];

        $location = Location::factory()->create();
        $parameter = Parameter::factory()->create(
            [
                'name'      => 'temperature',
                'source'    => OpenMeteoSource::SOURCE_NAME,
                'type'      => 'temperature',
                'valuetype' => 'float',
                'units'     => '°C',
            ]
        );
        WeatherData::factory()->create(
            [
                'parameter_id' => $parameter->id,
                'location_id'  => $location->id,
                'value'        => '20.0',
                'date'         => '2020-01-01 05:00:00',
                'source'       => OpenMeteoSource::SOURCE_NAME,
            ]
        );
        WeatherData::factory()->create(
            [
                'parameter_id' => $parameter->id,
                'location_id'  => $location->id,
                'value'        => '30.0',
                'date'         => '2020-01-02 05:00:00',
                'source'       => OpenMeteoSource::SOURCE_NAME,
            ]
        );
        $response = $this->postJson('/api/locations/'. $location->id. '/average-weather', $post);
        $response->assertStatus(200);
        $response->assertJson(
            [
                'startDate' => $post['start_date'],
                'finishDate' => $post['finish_date'],
                'values' => [
                    [
                        'parameter' => $parameter->name,
                        'avgvalue' => 25
                    ]
                ]
            ]
        );
    }

    public function test_location_average_from_different_period_success(): void
    {
        $post = [
            'start_date'  => '2024-01-01 00:00:00',
            'finish_date' => '2024-01-02 23:59:59'
        ];

        $location = Location::factory()->create();
        $parameter = Parameter::factory()->create(
            [
                'name'      => 'temperature',
                'source'    => OpenMeteoSource::SOURCE_NAME,
                'type'      => 'temperature',
                'valuetype' => 'float',
                'units'     => '°C',
            ]
        );
        WeatherData::factory()->create(
            [
                'parameter_id' => $parameter->id,
                'location_id'  => $location->id,
                'value'        => '20.0',
                'date'         => '2020-01-01 05:00:00',
                'source'       => OpenMeteoSource::SOURCE_NAME,
            ]
        );
        WeatherData::factory()->create(
            [
                'parameter_id' => $parameter->id,
                'location_id'  => $location->id,
                'value'        => '30.0',
                'date'         => '2020-01-02 05:00:00',
                'source'       => OpenMeteoSource::SOURCE_NAME,
            ]
        );
        $response = $this->postJson('/api/locations/'. $location->id. '/average-weather', $post);
        $response->assertStatus(200);
        $response->assertJson(
            [
                'startDate'  => $post['start_date'],
                'finishDate' => $post['finish_date'],
                'values'     => [],
            ]
        );
    }

    public function test_location_average_date_validation_error_success(): void
    {
        $post = [
            'start_date'  => '2024-01-01 00:00:00',
            'finish_date' => '2023-01-02 23:59:59'
        ];

        $location = Location::factory()->create();
        $response = $this->postJson('/api/locations/'. $location->id. '/average-weather', $post);
        $response->assertStatus(422);
        $response->assertJson(
            [
                'finish_date' => [
                    'The finish date field must be a date after start date.'
                ],
            ]
        );
    }
}
