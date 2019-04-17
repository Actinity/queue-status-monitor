<?php
namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Twogether\QueueStatus\QueueFetcher;

class EndpointTest
    extends TestCase
{
    public function test_queues_default_to_a_bad_response()
    {
        $this->get('queue-status-monitor')
            ->assertStatus(400);

    }

    public function test_only_default_queues_are_monitored_if_no_config() {
        $this->assertEquals(['default'],QueueFetcher::get());
    }

    public function test_monitored_queues_are_picked_up()
    {
        config(['queue.monitor' => ['queue','otherqueue']]);

        $this->assertEquals(['queue','otherqueue'],QueueFetcher::get());
    }

    public function test_a_single_string_can_be_monitored()
    {
        config(['queue.monitor' => 'myqueue']);

        $this->assertEquals(['myqueue'],QueueFetcher::get());
    }

    public function test_status_okay_if_queues_are_pinged()
    {
        Artisan::call('queue-status:ping');

        $this->get('queue-status-monitor')
            ->assertStatus(200);

    }

    public function test_status_okay_if_queues_are_manually_forced()
    {
        cache()->put('queue-status-monitor-default',time() - 100);

        $this->get('queue-status-monitor')
            ->assertStatus(200);

    }

    public function test_status_not_okay_if_queues_are_slow()
    {
        cache()->put('queue-status-monitor-default',time() - 10000);

        $this->get('queue-status-monitor')
            ->assertStatus(400);

    }

    public function test_multiple_queues_can_all_be_okay()
    {
        config(['queue.monitor' => ['timely','tardy']]);
        Artisan::call('queue-status:ping');

        $this->get('queue-status-monitor')
            ->assertStatus(200);


    }

    public function test_multiple_queues_must_all_be_okay()
    {
        config(['queue.monitor' => ['timely','tardy']]);
        Artisan::call('queue-status:ping');

        cache()->put('queue-status-monitor-tardy',time() - 10000);

        $this->get('queue-status-monitor')
            ->assertStatus(400);


    }

}