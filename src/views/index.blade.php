<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Queue Status</title>
</head>
<body>
    <table>
        @foreach($queues as $queue)
            <tr class="{{ $queue['class'] }}">
                <td>{{ $queue['name'] }}</td>
                <td>{{ $queue['delay'] }}</td>
                <td>{{ $queue['status'] }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>