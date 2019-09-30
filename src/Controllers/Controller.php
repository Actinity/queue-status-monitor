<?php
namespace Twogether\QueueStatus\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        $failed = $this->getFailedJobs();

        $okay = $okay && !$failed;

        return response()->view('queue-status-monitor::index',compact('queues','failed'),$okay ? 200 : 400);
    }

    private function getFailedJobs()
    {
        try {
            if(Schema::hasTable('failed_jobs')) {

                $failures = DB::table('failed_jobs')
                    ->orderBy('id')
                    ->select('failed_at')
                    ->get();

                if(count($failures)) {

                    return [
                        'number' => count($failures),
                        'earliest' => $failures->first()->failed_at,
                        'latest' => $failures->last()->failed_at
                    ];
                }

            }



        } catch (Exception $e) {
            // No database connection available. Do nothing.
        }

        return null;
    }
}