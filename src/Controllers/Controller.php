<?php

namespace Actinity\LaravelQueueStatus\Controllers;

use Actinity\LaravelQueueStatus\QueueStatusCheck;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function index()
    {
        return $this->response(
            (new QueueStatusCheck)
                ->withQueues()
                ->withMismatches()
                ->withFailedJobs()
        );
    }

    public function failed()
    {
        return $this->response(
            (new QueueStatusCheck)
                ->withFailedJobs()
        );
    }

    public function queuesOnly()
    {
        return $this->response(
            (new QueueStatusCheck)
                ->withQueues()
                ->withMismatches()
        );
    }

    private function response(QueueStatusCheck $check)
    {
        return response()->view(
            'queue-status-monitor::index',
            compact('check'),
            $check->okay() && ! config('queue.status_monitor_disabled') ? 200 : 400
        );
    }
}
