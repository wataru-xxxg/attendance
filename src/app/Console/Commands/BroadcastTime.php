<?php

namespace App\Console\Commands;

use App\Events\TimeUpdated;
use Illuminate\Console\Command;

class BroadcastTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcast:time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast current time every second';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        while (true) {
            try {
                broadcast(new TimeUpdated());
                sleep(1);
            } catch (\Exception $e) {
                $this->error('Error broadcasting time: ' . $e->getMessage());
            }
        }
    }
}
