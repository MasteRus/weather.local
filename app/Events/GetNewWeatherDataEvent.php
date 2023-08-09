<?php

namespace App\Events;

use App\Models\Location;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GetNewWeatherDataEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public Location $location;
    public string $startDate;
    public string $finishDate;

    public function __construct(Location $location, string $startDate, string $finishDate)
    {
        $this->location = $location;
        $this->startDate = $startDate;
        $this->finishDate = $finishDate;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('GetNewWeatherDataEvent'),
        ];
    }
}
