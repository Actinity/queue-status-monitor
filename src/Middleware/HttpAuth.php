<?php

namespace Twogether\QueueStatus\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class HttpAuth
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (!$this->canLogin($request)) {
            header('WWW-Authenticate: Basic realm="QS Monitoring"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Sorry, authorisation is required';
            exit;
        }

        return $next($request);
    }

    private function canLogin($request)
    {
        if($request->header('PHP_AUTH_USER') != 'twogether') {
            return false;
        }

        if($request->header('PHP_AUTH_PW') != 'queues') {
            return false;
        }

        return true;

    }
}
