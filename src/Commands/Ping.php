<?php
namespace Actinity\LaravelQueueStatus\Commands;

use Illuminate\Console\Command;
use Actinity\LaravelQueueStatus\Jobs\PingQueue;
use Actinity\LaravelQueueStatus\QueueFetcher;

class Ping
    extends Command
{
    protected $signature = "queue-status:ping";

    protected $description = "Dispatch test jobs on all queues";

    public function handle()
    {
		cache()->forever('queue-status-monitor-cron',now()->toDateTimeString());
        foreach(QueueFetcher::get() as $queue) {
            PingQueue::dispatch($queue,time())->onQueue($queue->name);
        }
    }

}