<?php

namespace App\Jobs;

use App\Models\Location;
use App\Repositories\IParameterRepository;
use App\Repositories\IWeatherDataRepository;
use App\Service\WeatherDataSource\OpenMeteoParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OpenMeteoHistoricalParserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $response;
    private string $startDate;
    private string $finishDate;
    private Location $location;

    public function __construct(?array $response, string $startDate, string $finishDate, Location $location)
    {
        $this->response = $response;
        $this->startDate = $startDate;
        $this->finishDate = $finishDate;
        $this->location = $location;
    }


    /**
     * Execute the job.
     */
    public function handle(
        IParameterRepository $parameterRepository,
        IWeatherDataRepository $weatherDataRepository
    ): void {
        Log::info("==========START OpenMeteoParser Job ================");
        $service = new OpenMeteoParser($parameterRepository, $weatherDataRepository);
        $service->parse($this->response, $this->startDate, $this->finishDate, $this->location);
        Log::info("==========START Finish Job ================");
    }
}
