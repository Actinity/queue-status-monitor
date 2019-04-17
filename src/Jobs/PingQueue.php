<?php
namespace Twogether\QueueStatus\Jobs;

class PingQueue
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function handle()
    {
        cache()->put('queue-status-monitor-'.$this->name,time());
    }
}