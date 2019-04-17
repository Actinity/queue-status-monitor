<?php
namespace Twogether\QueueStatus;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Twogether\QueueStatus\Commands\Ping;

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

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('queue-status:ping')->everyMinute();
        });

        $this->loadViewsFrom(__DIR__."/views", 'queue-status-monitor');

    }

    public function register()
    {

    }
}