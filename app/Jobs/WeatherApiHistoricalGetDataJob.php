<?php

namespace App\Jobs;

use App\Models\Location;
use App\Service\WeatherDataSource\WeatherApiSource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WeatherApiHistoricalGetDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $startDate;
    private string $finishDate;
    private Location $location;

    public function __construct(string $startDate, string $finishDate, Location $location)
    {
        $this->startDate = $startDate;
        $this->finishDate = $finishDate;
        $this->location = $location;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("==========START WeatherApiGetData Job ================");
        $service = new WeatherApiSource();
        $service->getData($this->startDate, $this->finishDate, $this->location);
        Log::info("==========FINISH WeatherApiGetData Job ================");
    }
}
