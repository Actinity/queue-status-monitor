<?php
namespace Twogether\QueueStatus;

class MonitoredQueue
    implements \ArrayAccess
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
        switch($value) {
            case "name": return $this->getName();
            case "threshold": return $this->getThreshold();
            case "cache_key": return $this->getCacheKey();
            default: return null;
        }
    }

    public function getCacheKey(): string
    {
        return 'queue-status-monitor-'.$this->name;
    }


    public function offsetExists($offset)
    {
        return in_array($offset,['name','threshold','cache_key']);
    }

    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }
}