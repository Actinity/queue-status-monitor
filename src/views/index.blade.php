<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Queue Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Helvetica,sans-serif;
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 500px;
            max-width: 100%;
        }
        td,th {
            padding: 10px;
            text-align: left;
            white-space:nowrap;
        }
        th {
            border-bottom: 1px solid #999;
        }

        .no-data {
            color: #999;
        }
        .okay {
            color: #090;
        }
        .failing {
            color: #f00;
        }
    </style>
</head>
<body>
@if($check->queuesWereChecked())

<h3>Queues</h3>

<p>Last dispatch:
@if($check->lastcron())
    {{ $check->lastcron()->diffForHumans() }} - {{ $check->lastcron() }}
@else
    <span class="failing">NEVER - Check schedule/cron configuration.</span>
@endif
</p>

<table>
    <thead>
    <tr>
        <th>Queue</th>
        <th>Last Run</th>
        <th>Last Delay</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($check->queues() as $queue)
        <tr class="{{ $queue['class'] }}">
            <td>{{ $queue['name'] }}</td>
            <td title="{{ $queue['last_run'] }}">
                @if($queue['last_run'] === '-')
                    -
                @else
                    {{ \Carbon\Carbon::parse($queue['last_run'])->diffForHumans() }}
                @endif
            </td>
            <td>{{ $queue['delay'] }}</td>
            <td>{{ $queue['status'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@endif

@if($check->hasMismatches())

    <h3 style="color:#f00;">Some queues have <i>retry_after</i> set shorter or identical to their <i>timeout</i></h3>
    <table>
        <thead>
        <tr>
            <th>Connection</th>
            <th>Queue</th>
            <th>Timeout</th>
            <th>Retry After</th>
        </tr>
        </thead>
        <tbody>
        @foreach($check->mismatches() as $mismatch)
            <tr>
                <td>{{ $mismatch->connection }}</td>
                <td>{{ $mismatch->queue }}</td>
                <td>{{ $mismatch->timeout }}</td>
                <td>{{ $mismatch->retry_after }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>

@endif

@if($check->hasFailures())

    <h3>Failed jobs</h3>

    <table>
        <tbody>
        <tr>
            <td>Failed jobs</td>
            <td>{{ $check->failures() }}</td>
        </tr>
        <tr>
            <td>Earliest</td>
            <td>{{ $check->firstFailed() }}</td>
        </tr>
        <tr>
            @if($check->firstFailed() != $check->lastFailed())
                <td>Latest</td>
                <td>{{ $check->lastFailed() }}</td>
            @endif
        </tr>
        </tbody>
    </table>

@elseif($check->couldNotCheckFailures())
    <p class="failing"><strong>Warning:</strong> Failed jobs could not be checked.</p>

@elseif($check->triedCheckingFailures())
    <p>No failed jobs to report</p>
@endif

</body>
</html>
