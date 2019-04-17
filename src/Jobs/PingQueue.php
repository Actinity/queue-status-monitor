<?php
namespace Twogether\QueueStatus\Jobs;

use Twogether\QueueStatus\MonitoredQueue;

class PingQueue
{
    private $queue;

    public function __construct(MonitoredQueue $queue)
    {
        $this->queue = $queue;
    }

    public function handle()
    {
        cache()->put($this->queue->cache_key,time());
    }
}