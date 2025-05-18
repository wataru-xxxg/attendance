<?php

namespace App\Http\Livewire;

use Livewire\Component;

class RealtimeClock extends Component
{
    public $currentTime;
    public $currentDate;
    public $now;

    protected $listeners = ['echo:time-channel,.TimeUpdated' => 'updateTime'];

    public function mount()
    {
        $this->currentTime = now()->format('H:i');
        $this->currentDate = now()->isoFormat('YYYY年MM月DD日(ddd)');
        $this->now = now();
    }

    public function updateTime($event)
    {
        $this->currentTime = $event['time'];
        $this->currentDate = $event['date'];
        $this->now = $event['now'];
    }

    public function render()
    {
        return view('livewire.realtime-clock');
    }
}
