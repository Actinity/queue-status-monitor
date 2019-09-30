<?php

Route::group([
    'namespace' => '\Twogether\QueueStatus\Controllers',
    'middleware' => [Twogether\QueueStatus\Middleware\HttpAuth::class]
],function() {

    Route::get('queue-status-monitor','Controller@test');

});
