<?php
namespace Actinity\LaravelQueueStatus\Controllers;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Actinity\LaravelQueueStatus\QueueFetcher;

class Controller
    extends BaseController
{
    public function index()
    {
        $okay = true;
        $queues = [];

		$last_cron = cache()->get('queue-status-monitor-cron');
		$last_cron = $last_cron ? Carbon::parse($last_cron) : null;

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

        $failed = null;

        if(!config('queue.do_not_monitor_failed_jobs')) {
            $failed = $this->getFailedJobs();
            $okay = $okay && !$failed;
        }

        $mismatches = $this->getMismatches();

        if(count($mismatches)) {
            $okay = false;
        }

        return response()->view(
			'queue-status-monitor::index',
			compact('queues','failed','mismatches','last_cron'),
			$okay ? 200 : 400
		);
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

    private function getMismatches()
    {
        $mismatches = [];
        if(cache()->get('queue-status-monitor-mismatches')) {
            foreach(config('queue.connections') as $connection => $details) {
                foreach(QueueFetcher::get() as $queue) {
                    if($data = cache()->get("queue-status-monitor-mismatch-{$connection}-{$queue->getName()}")) {
                        $mismatches[] = json_decode($data);
                    }
                }
            }
        }
        return $mismatches;
    }
}
