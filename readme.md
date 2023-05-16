# Laravel Queue Status Monitor

Once installed this will add itself to the schedule and dispatch a job
every minute on the `default` queue which updates a cached value.

The `/queue-status-monitor` endpoint shows the status of the monitor and
will return either 200 OK or 400 if queues have not run within the
expected threshold (5 minutes by default. This endpoint can then be
monitored by Pingdom or similar.

If you are using multiple queues, or if you wish to customise thresholds
you can add a `monitor` array to `config/queue.php`. Examples are:


### Monitor a single queue

```
    "monitor" => "myqueue"
```

### Monitor multiple queues

```
    "monitor" => [
        "myqueue",
        "myotherqueue"
    ]
```

### Monitor a queue with a custom threshold

```
    "monitor" => [
        ["name" => myqueue","threshold" => 600],
    ]
```

Threshold values are specified in seconds and represent the point
where the monitor will report that the queue is failing if the
job has not run within that period.


## Delay
As well as reporting the last run time, the `/queue-status-monitor`
endpoint also reports the `delay`. This is the time between the job
being dispatched and handled. This does not impact the overall status
and will not trigger failures. It is purely for reference.


## Failed Jobs
By default, if you have a failed_jobs table, then any failures will
be treated as errors and generate a 400 status code. You can disable
this by adding the following to `config/queue.php`.

`"do_not_monitor_failed_jobs" => true`

Alternatively, if you are prepared to tolerate failed jobs, but don't
want them to stop you knowing if there is another issue; you can monitor 
independent endpoints rather than just the default:

`queue-status-monitor/without-failed` will ignore failed jobs and
only report on thresholds and misconfiguration.

`queue-status-monitor/only-failed` will only check failed jobs.


## Timeout vs retry_after misconfiguration checks
Queue workers are typically configured via Supervisor or similar, and
the `queue:work` command includes a `--timeout` property. If this is set
higher than the corresponding `retry_after` property (in `config.queue`) 
it can cause jobs to be duplicated. The monitor checks for this and will
alert in the endpoint (and throw a 400 response).


## Last dispatch

The `Last dispatch` value at the top of the queue monitor indicates the
last time the `queue-status:ping` command was dispatched by the scheduler 
(typically every minute if you're using standard Laravel cron
configuration). This doesn't trigger any errors by itself, but is shown 
for reference to help debug if the issue is with your crontab.


## Auth
If you want to protect your monitor endpoints, add:

`"status_password" => "YOURPASSWORD"`

To the queue config. The default username is 'queues', but you can 
customise this as well by setting:

`"status_user" => "USERNAME"`