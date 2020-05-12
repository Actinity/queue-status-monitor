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

        $timeout = $this->job->timeout();
        $retry_after = (int) config("queue.connections.{$this->job->getConnectionName()}.retry_after");

        if(array_key_exists('argv',$_SERVER)) {
            foreach($_SERVER['argv'] as $value) {
                if(substr($value,0,10) === '--timeout=') {
                    $timeout = (int) substr($value,10);
                }
            }
        }

        if($timeout && $timeout >= $retry_after) {
            cache()->put('queue-status-monitor-mismatches',1,new \DateInterval('PT1M'));

            cache()->put(
                $this->getTimeoutMismatchKey($this->job->getConnectionName(),$this->job->getQueue()),
                json_encode([
                    'connection' => $this->job->getConnectionName(),
                    'queue' => $this->job->getQueue(),
                    'retry_after' => $retry_after,
                    'timeout' => $timeout
                ]),
                new \DateInterval('PT1M')
            );
        }


    }

    public function getTimeoutMismatchKey(string $connection,string $queue)
    {
        return "queue-status-monitor-mismatch-{$connection}-{$queue}";
    }
}