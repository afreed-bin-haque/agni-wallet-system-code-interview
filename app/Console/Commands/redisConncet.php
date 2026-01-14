<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class redisConncet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:ping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping Redis server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pong = Redis::ping();

        if ($pong) {
            $this->info('Redis server is reachable.');
        } else {
            $this->error('Unable to reach Redis server.');
        }
    }
}
