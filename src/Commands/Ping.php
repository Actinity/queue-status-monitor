<?php
namespace Twogether\QueueStatus\Commands;

use Illuminate\Console\Command;
use Twogether\QueueStatus\Jobs\PingQueue;
use Twogether\QueueStatus\QueueFetcher;

class Ping
    extends Command
{
    protected $signature = "queue-status:ping";

    protected $description = "Ping okay for each queue";

    public function handle()
    {
        $queues = QueueFetcher::get();

        foreach($queues as $queue) {
            dispatch(new PingQueue($queue));
        }
    }

}