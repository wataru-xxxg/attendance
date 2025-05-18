<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimeUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $time;
    public $date;
    public $now;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->time = now()->format('H:i');
        $this->date = now()->isoFormat('YYYY年MM月DD日(ddd)');
        $this->now = now();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('time-channel');
    }

    /**
     * イベント名を指定
     */
    public function broadcastAs()
    {
        return 'TimeUpdated';
    }

    public function broadcastWith()
    {
        return [
            'time' => $this->time,
            'date' => $this->date,
            'now' => $this->now
        ];
    }
}
