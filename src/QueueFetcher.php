<?php

namespace Actinity\LaravelQueueStatus;

use Illuminate\Support\Collection;

class QueueFetcher
{
    public static function get(): Collection
    {
        $config = config('queue.monitor');

        $queues = [];

        if ($config) {

            if (! is_array($config)) {
                $config = [$config];
            }

            foreach ($config as $queue) {

                if (! is_array($queue)) {
                    $queues[] = new MonitoredQueue($queue);
                } else {
                    $queues[] = new MonitoredQueue($queue['name'], $queue['threshold'] ?? null);
                }
            }

        } else {
            $queues[] = new MonitoredQueue('default');
        }

        return collect($queues);
    }
}
