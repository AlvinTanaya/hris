<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Schedule</title>
</head>

<body>
    <h2>Dear {{ $name }},</h2>
    <p>Your interview has been scheduled on:</p>
    <p><strong>Date:</strong> {{ $interview_date }}</p>
    
    <p><strong>Note:</strong></p>
    <p>{!! nl2br(e($interview_note)) !!}</p>

    <p>Best Regards,<br>HR Team</p>
</body>

</html>
