<!DOCTYPE html>
<html>
<head>
    <title>New Shift Assignment</title>
</head>
<body>
    <p>Hello {{ $name }},</p>

    <p>You have been assigned to a new shift: <strong>{{ $type }}</strong>.</p>

    <p><strong>Schedule details:</strong></p>
    <p>Start Date: {{ $start_date }}</p>
    <p>End Date: {{ $end_date }}</p>

    <p><strong>Shift Schedule:</strong></p>
    <pre>{{ $scheduleDetails }}</pre>

    <p>Please check your schedule and contact the Human Resources department if you have any concerns.</p>

    <p>Thank you.</p>

    <p>Best regards,</p>
    <p>PT. Timur Jaya Indosteel</p>
</body>
</html>
