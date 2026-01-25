<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <h2>{{ $payload['title'] ?? 'Notification' }}</h2>

    <p>{{ $payload['message'] ?? '' }}</p>

    @if (!empty($payload['meta']))
        <pre>{{ json_encode($payload['meta'], JSON_PRETTY_PRINT) }}</pre>
    @endif

    <hr>
    <small>This email was sent automatically.</small>
</body>
</html>
