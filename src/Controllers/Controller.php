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
                ->withSizes(request()->has('size'))
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
                ->withSizes(request()->has('size'))
        );
    }

    private function response(QueueStatusCheck $check)
    {
        return response()->view(
            'queue-status-monitor::index',
            [
                'check' => $check,
                'size' => request()->has('size'),
            ],
            $check->okay() && ! config('queue.status_monitor_disabled') ? 200 : 400
        );
    }
}
