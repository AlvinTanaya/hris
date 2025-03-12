<!DOCTYPE html>
<html>
<head>
    <title>Warning Letter</title>
</head>
<body>
    <p>Dear {{ $user->name }},</p>

    @if ($count == 1)
        <p>We gave you your first warning letter.</p>
    @else
        <p>We gave you warning letter #{{ $count }}.</p>
    @endif

    <p>The reason: <strong>{{ $reason }}</strong></p>

    @if ($count >= 3)
        <p>If you receive 3 warnings, you will be terminated.</p>
    @endif

    <p>Issued by: {{ $maker->name }}</p>

    <p>Best regards,</p>
    <p>Your Company</p>
</body>
</html>
