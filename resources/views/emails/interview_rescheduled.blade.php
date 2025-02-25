<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Rescheduled</title>
</head>
<body>
    <h2>Dear {{ $name }},</h2>
    <p>We would like to inform you that your interview schedule has been changed.</p>
    <p><strong>Previous Date:</strong> {{ $old_interview_date }}</p>
    <p><strong>New Date:</strong> {{ $new_interview_date }}</p>

    <p><strong>Note:</strong></p>
    <p>{!! nl2br(e($interview_note)) !!}</p>

    <p>We apologize for any inconvenience caused. Please confirm your availability for the new schedule.</p>

    <p>Best Regards,<br>HR Team</p>
</body>
</html>
