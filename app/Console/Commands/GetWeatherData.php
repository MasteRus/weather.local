<?php

namespace App\Console\Commands;

use App\Events\GetNewWeatherDataEvent;
use App\Models\Location;
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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = $this->argument('startDate') ?? date('Y-m-d', strtotime('-1 day'));
        $finishDate = $this->argument('finishDate') ?? date('Y-m-d', strtotime('-1 day'));

        foreach (Location::all() as $loc) {
            GetNewWeatherDataEvent::dispatch($loc, $startDate, $finishDate);
        }
    }
}
