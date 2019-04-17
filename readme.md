#Queue Status Monitor

Once installed this will add itself to the schedule and dispatch a job
every minute on the `default` queue which updates a cached value.

The `/queue-status-monitor` endpoint shows the status of the monitor and
will return either 200 OK or 400 if queues have not run within the
expected threshold (5 minutes by default. This endpoint can be
monitored by Pingdom.

If you are using multiple queues, or if you wish to customise thresholds
you can add a `monitor` array to `config/queue.php`. Examples are:


###Monitor a single queue

```
    "monitor" => "myqueue"
```

###Monitor multiple queues

```
    "monitor" => [
        "myqueue",
        "myotherqueue"
    ]
```

###Monitor a queue with a custom threshold

```
    "monitor" => [
        ["name" => myqueue","threshold" => 600]
    ]
```

Threshold values are specified in seconds and represent the point
where the monitor will report that the queue is failing if the
job has not run within that period.


##Delay
As well as reporting the last run time, the `/queue-status-monitor`
endpoint also reports the `delay`. This is the time between the job
being dispatched and handled. This does not impact the overall status
and will not trigger failures. It is purely for reference.