<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => '\Actinity\LaravelQueueStatus\Controllers',
    'middleware' => [Actinity\LaravelQueueStatus\Middleware\HttpAuth::class],
    'prefix' => config('queue.monitor-prefix', 'queue-status-monitor'),
], function () {

    Route::get('', 'Controller@index');

    Route::get('only-failed', 'Controller@failed');
    Route::get('without-failed', 'Controller@queuesOnly');

});
