<?php
namespace Twogether\QueueStatus\Commands;

use Illuminate\Console\Command;
use Twogether\QueueStatus\Jobs\PingQueue;
use Twogether\QueueStatus\QueueFetcher;

class Ping
    extends Command
{
    protected $signature = "queue-status:ping";

    protected $description = "Dispatch test jobs on all queues";

    public function handle()
    {
        foreach(QueueFetcher::get() as $queue) {
            PingQueue::dispatch($queue,time())->onQueue($queue->name);
        }
    }

}