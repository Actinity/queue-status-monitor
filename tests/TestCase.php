<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Actinity\LaravelQueueStatus\QueueStatusServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

    protected function getPackageProviders($app)
    {
        return [
            QueueStatusServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

}