<?php
namespace Actinity\LaravelQueueStatus;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Actinity\LaravelQueueStatus\Commands\Ping;

class QueueStatusServiceProvider
    extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__."/routes.php");

        if ($this->app->runningInConsole()) {
            $this->commands([
                Ping::class,
            ]);
        }

		if(!config('queue.status_monitor_disabled')) {
			$this->app->booted(function () {
				$schedule = $this->app->make(Schedule::class);
				$schedule->command('queue-status:ping')->everyMinute()->onOneServer();
			});
		}

        $this->loadViewsFrom(__DIR__."/views", 'queue-status-monitor');

    }

    public function register()
    {

    }
}