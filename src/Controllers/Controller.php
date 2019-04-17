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
            $status = cache()->get($queue->cache_key);

            if(!$status) {
                $okay = false;
                $status = [
                    'name' => $queue->name,
                    'delay' => '-',
                    'last_run' => '-',
                    'status' => 'No data',
                    'class' => 'no-data',
                ];
            } else {
                $last_run_offset = time() - $status['last_run'];

                if ($last_run_offset > $queue->threshold) {
                    $status = [
                        'name' => $queue->name,
                        'delay' => $status['delay'],
                        'last_run' => date("r",$status['last_run']),
                        'status' => 'Over threshold',
                        'class' => 'failing',
                    ];
                    $okay = false;
                } else {
                    $status = [
                        'name' => $queue->name,
                        'delay' => $status['delay'],
                        'last_run' => date("r",$status['last_run']),
                        'status' => 'Under threshold',
                        'class' => 'okay',
                    ];
                }

            }

            $queues[] = $status;
        }

        return response()->view('queue-status-monitor::index',compact('queues'),$okay ? 200 : 400);
    }
}