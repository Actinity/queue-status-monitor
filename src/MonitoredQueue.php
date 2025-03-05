<?php

namespace Actinity\LaravelQueueStatus;

class MonitoredQueue
{
    const DEFAULT_THRESHOLD = 300;

    private $name;

    private $threshold;

    public function __construct(string $name, ?int $threshold = null)
    {
        $this->name = $name;
        $this->threshold = $threshold ?: $this::DEFAULT_THRESHOLD;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getThreshold(): int
    {
        return $this->threshold;
    }

    public function __get($value)
    {
        switch ($value) {
            case 'name': return $this->getName();
            case 'threshold': return $this->getThreshold();
            case 'cache_key': return $this->getCacheKey();
            default: return null;
        }
    }

    public function getCacheKey(): string
    {
        return 'queue-status-monitor-'.$this->name;
    }
}
