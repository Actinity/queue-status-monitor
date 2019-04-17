<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Queue Status</title>
</head>
<body>
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
        @foreach($queues as $queue)
            <tr class="{{ $queue['class'] }}">
                <td>{{ $queue['name'] }}</td>
                <td>{{ $queue['last_run'] }}</td>
                <td>{{ $queue['delay'] }}</td>
                <td>{{ $queue['status'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>