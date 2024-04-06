<?php

namespace App\Jobs;

use App\Exceptions\DataSourceMisconfigurationException;
use App\Models\Location;
use App\Service\WeatherDataSource\BaseWeatherDataSource;
use App\Service\WeatherDataSource\WeatherSourceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DataSourceReceiveDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $source;
    private string $startDate;
    private string $finishDate;
    private Location $location;

    public function __construct(string $startDate, string $finishDate, Location $location, string $source)
    {
        $this->source = $source;
        $this->startDate = $startDate;
        $this->finishDate = $finishDate;
        $this->location = $location;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("==========START DataSourceReceiveData Job ================");
        if (!class_exists($this->source)) {
            Log::error('DataSource ' . $this->source . ' misconfiguration error');

            throw new DataSourceMisconfigurationException();
        }
        /** @var WeatherSourceInterface $dataSource */
        $dataSource = app($this->source);
        if (!($dataSource instanceof WeatherSourceInterface)) {
            Log::error('DataSource ' . $this->source . ' Is not instance of source');
            throw new DataSourceMisconfigurationException();
        }
        $dataSource->dispatchGetResponseJob($this->startDate, $this->finishDate, $this->location);
        Log::info("==========FINISH DataSourceReceiveData Job ================");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("DataSourceRecieve error", ['error' => $exception]);
    }
}
