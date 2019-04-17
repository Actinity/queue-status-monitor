<?php
namespace Twogether\QueueStatus\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Twogether\QueueStatus\QueueFetcher;

class Controller
    extends BaseController
{
    public function test()
    {
        $okay = true;

        foreach(QueueFetcher::get() as $queue) {
            $time = cache()->get('queue-status-monitor-'.$queue);

            if (!$time || $time < time() - 300) {
                $okay = false;
            }
        }

        if ($okay) {
            return 'ok';
        }

        abort(400);
    }
}