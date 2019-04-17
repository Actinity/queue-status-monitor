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
        $queues = [];

        foreach(QueueFetcher::get() as $queue) {
            $time = cache()->get($queue->cache_key);

            if(!$time) {
                $okay = false;
                $status = [
                    'name' => $queue->name,
                    'delay' => 0,
                    'status' => 'No data',
                    'class' => 'no-data',
                ];
            } else {
                $delay = time() - $time;

                if ($delay > $queue->threshold) {
                    $status = [
                        'name' => $queue->name,
                        'delay' => $delay,
                        'status' => 'Over threshold',
                        'class' => 'failing',
                    ];
                    $okay = false;
                } else {
                    $status = [
                        'name' => $queue->name,
                        'delay' => $delay,
                        'status' => 'Under threshold',
                        'class' => 'okay',
                    ];
                }

            }

            $queues[] = $status;
        }

        if ($okay) {
            return 'ok';
        }

        abort(400);
    }
}