<?php

Route::group([
    'namespace' => '\Actinity\LaravelQueueStatus\Controllers',
    'middleware' => [Actinity\LaravelQueueStatus\Middleware\HttpAuth::class]
],function() {

    Route::get('queue-status-monitor','Controller@test');

});
