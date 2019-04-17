<?php

Route::group([
    'namespace' => '\Twogether\QueueStatus\Controllers',
],function() {

    Route::get('queue-status-monitor','Controller@test');

});