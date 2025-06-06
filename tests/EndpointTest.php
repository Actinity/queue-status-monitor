<?php

namespace Tests;

use Actinity\LaravelQueueStatus\MonitoredQueue;
use Actinity\LaravelQueueStatus\QueueFetcher;
use Illuminate\Support\Facades\Artisan;

class EndpointTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['queue.status_password' => 'none']);
    }

    public function test_queues_default_to_a_bad_response()
    {
        $this->get('queue-status-monitor')
            ->assertStatus(400);

    }

    public function test_only_default_queues_are_monitored_if_no_config()
    {
        $this->assertEquals('default', QueueFetcher::get()->first()->getName());
    }

    public function test_monitored_queues_are_picked_up()
    {
        config(['queue.monitor' => ['queue', 'otherqueue']]);

        $this->assertEquals(['queue', 'otherqueue'], QueueFetcher::get()->map(fn ($queue) => $queue->getName())->all());
    }

    public function test_a_single_string_can_be_monitored()
    {
        config(['queue.monitor' => 'myqueue']);

        $this->assertEquals('myqueue', QueueFetcher::get()->first()->getName());
    }

    public function test_status_okay_if_queues_are_pinged()
    {
        Artisan::call('queue-status:ping');

        $this->get('queue-status-monitor')
            ->assertStatus(200);

    }

    public function test_thresholds_default()
    {
        config(['queue.monitor' => [['name' => 'myqueue']]]);

        $this->assertEquals(MonitoredQueue::DEFAULT_THRESHOLD, QueueFetcher::get()->first()->getThreshold());

    }

    public function test_thresholds_can_be_set()
    {
        config(['queue.monitor' => [['name' => 'myqueue', 'threshold' => 900]]]);

        $this->assertEquals(900, QueueFetcher::get()->first()->getThreshold());

    }

    public function test_status_okay_if_queues_are_manually_forced()
    {
        cache()->put('queue-status-monitor-default', ['last_run' => time() - 100, 'delay' => 0]);

        $this->get('queue-status-monitor')
            ->assertStatus(200);

    }

    public function test_status_not_okay_if_queues_are_slow()
    {
        cache()->put('queue-status-monitor-default', ['last_run' => time() - 10000, 'delay' => 0]);

        $this->get('queue-status-monitor')
            ->assertStatus(400);

    }

    public function test_multiple_queues_can_all_be_okay()
    {
        config(['queue.monitor' => ['timely', 'tardy']]);
        Artisan::call('queue-status:ping');

        $this->get('queue-status-monitor')
            ->assertStatus(200);

    }

    public function test_multiple_queues_must_all_be_okay()
    {
        config(['queue.monitor' => ['timely', 'tardy']]);
        Artisan::call('queue-status:ping');

        cache()->put('queue-status-monitor-tardy', ['last_run' => time() - 10000, 'delay' => 0]);

        $this->get('queue-status-monitor')
            ->assertStatus(400);
    }
}
