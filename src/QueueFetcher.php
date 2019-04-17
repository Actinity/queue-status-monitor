<?php
namespace Twogether\QueueStatus;

class QueueFetcher
{
    public static function get()
    {
        $config = config('queue.monitor');

        if($config) {
            if(is_array($config)) {
                return $config;
            } elseif ($config) {
                return [$config];
            }
        }

        return ['default'];
    }
}