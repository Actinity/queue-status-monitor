<?php

namespace Actinity\LaravelQueueStatus\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HttpAuth
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (!$this->canLogin($request)) {
            return response()->make('Sorry, authorisation is required',401,[
                'WWW-Authenticate' => 'Basic realm="QS Monitoring"',
            ]);
        }

        return $next($request);
    }

    private function canLogin($request)
    {
        if(!config('queue.status_password') || config('queue.status_password') === 'none') {
            return true;
        }
        if($request->header('PHP_AUTH_USER') != config('queue.status_user','queues')) {
            return false;
        }

        if($request->header('PHP_AUTH_PW') != config('queue.status_password')) {
            return false;
        }

        return true;

    }
}
