<?php

namespace App\Console\Commands;

use App\Service\WeatherDataUpdater;
use Illuminate\Console\Command;

class GetWeatherData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "app:weather-data-get {startDate?} {finishDate?}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private WeatherDataUpdater $weatherDataUpdater;

    public function __construct(WeatherDataUpdater $weatherDataUpdater)
    {
        parent::__construct();
        $this->weatherDataUpdater = $weatherDataUpdater;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = $this->argument('startDate') ?? date('Y-m-d', strtotime('-1 day'));
        $finishDate = $this->argument('finishDate') ?? date('Y-m-d', strtotime('-1 day'));

        $this->weatherDataUpdater->getData($startDate, $finishDate);
    }
}
