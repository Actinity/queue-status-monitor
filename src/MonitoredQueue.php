<?php
namespace Actinity\LaravelQueueStatus;

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


    public function offsetExists(mixed $offset): bool
	{
        return in_array($offset,['name','threshold','cache_key']);
    }

    public function offsetGet($offset): mixed
	{
        return $this->$offset;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset(mixed $offset): void
    {
        // TODO: Implement offsetUnset() method.
    }
}