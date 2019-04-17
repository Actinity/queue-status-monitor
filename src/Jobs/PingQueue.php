<?php
namespace Twogether\QueueStatus\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Twogether\QueueStatus\MonitoredQueue;

class PingQueue
    implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    private $monitoredQueue;
    private $requestedAt;

    public function __construct(MonitoredQueue $queue,int $requested_at)
    {
        $this->monitoredQueue = $queue;
        $this->requestedAt = $requested_at;
    }

    public function handle()
    {
        cache()->forever($this->monitoredQueue->cache_key,[
            'last_run' => time(),
            'delay' => time() - $this->requestedAt,
        ]);
    }
}