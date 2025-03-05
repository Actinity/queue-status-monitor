<?php

namespace Actinity\LaravelQueueStatus;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;

class QueueStatusCheck
{
    private array $_queues = [];

    private int $_failures_number = 0;

    private $_failures_earliest = null;

    private $_failures_latest = null;

    private array $_mismatches = [];

    private int $_issues = 0;

    private bool $_failures_were_attempted = false;

    private bool $_failure_check_failed = false;

    private $_queues_were_checked = false;

    private $_sizes_requested = false;

    public function withQueues(): self
    {
        $this->_queues_were_checked = true;

        foreach (QueueFetcher::get() as $queue) {
            $status = cache()->get($queue->cache_key);

            if (! $status) {
                $this->_issues++;
                $status = [
                    'name' => $queue->name,
                    'delay' => '-',
                    'last_run' => '-',
                    'status' => 'No data',
                    'class' => 'no-data',
                ];
            } else {
                $last_run_offset = time() - $status['last_run'];

                if ($last_run_offset > $queue->threshold) {
                    $status = [
                        'name' => $queue->name,
                        'delay' => $status['delay'],
                        'last_run' => date('r', $status['last_run']),
                        'status' => 'Over threshold',
                        'class' => 'failing',
                    ];
                    $this->_issues++;
                } else {
                    $status = [
                        'name' => $queue->name,
                        'delay' => $status['delay'],
                        'last_run' => date('r', $status['last_run']),
                        'status' => 'Under threshold',
                        'class' => 'okay',
                    ];
                }

            }

            if ($this->_sizes_requested) {
                $status['size'] = Queue::size($queue->name);
            }

            $this->_queues[] = $status;
        }

        return $this;
    }

    public function withSizes(bool $setting): self
    {
        $this->sizes_requested = $setting;
        if ($setting && $this->_queues_were_checked) {
            $this->_queues = Arr::map($this->_queues, fn ($queue) => [...$queue, 'size' => Queue::size($queue['name'])]);
        }

        return $this;
    }

    public function withFailedJobs(): self
    {
        if (config('queue.do_not_monitor_failed_jobs')) {
            return $this;
        }

        $this->_failures_were_attempted = true;

        try {
            if (Schema::hasTable('failed_jobs')) {
                $failures = DB::table('failed_jobs')
                    ->orderBy('id')
                    ->select('failed_at')
                    ->get();

                if (count($failures)) {
                    $this->_failures_number = count($failures);
                    $this->_failures_earliest = $failures->first()->failed_at;
                    $this->_failures_latest = $failures->last()->failed_at;
                    $this->_issues++;
                }
            }

        } catch (Exception $e) {
            // No database connection available. Do nothing.

            $this->_failure_check_failed = true;
        }

        return $this;
    }

    public function queuesWereChecked(): bool
    {
        return $this->_queues_were_checked;
    }

    public function triedCheckingFailures(): bool
    {
        return $this->_failures_were_attempted;
    }

    public function couldNotCheckFailures(): bool
    {
        return $this->_failure_check_failed;
    }

    public function withMismatches(): self
    {
        if (cache()->get('queue-status-monitor-mismatches')) {
            foreach (config('queue.connections') as $connection => $details) {
                foreach (QueueFetcher::get() as $queue) {
                    if ($data = cache()->get("queue-status-monitor-mismatch-{$connection}-{$queue->getName()}")) {
                        $this->_mismatches[] = json_decode($data);
                        $this->_issues++;
                    }
                }
            }
        }

        return $this;
    }

    public function okay(): bool
    {
        return $this->_issues === 0;
    }

    public function hasMismatches(): bool
    {
        return (bool) count($this->_mismatches);
    }

    public function queues(): array
    {
        return $this->_queues;
    }

    public function hasFailures(): bool
    {
        return (bool) $this->_failures_number;
    }

    public function failures(): int
    {
        return $this->_failures_number;
    }

    public function firstFailed(): ?string
    {
        return $this->_failures_earliest;
    }

    public function lastFailed(): ?string
    {
        return $this->_failures_latest;
    }

    public function mismatches(): array
    {
        return $this->_mismatches;
    }

    public function lastcron(): ?Carbon
    {
        $last_cron = cache()->get('queue-status-monitor-cron');

        return $last_cron ? Carbon::parse($last_cron) : null;
    }
}
