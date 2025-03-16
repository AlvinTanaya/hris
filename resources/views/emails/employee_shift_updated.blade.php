<!DOCTYPE html>
<html>
<head>
    <title>Shift Assignment Update</title>
</head>
<body>
    <p>Hello {{ $name }},</p>

    <p><strong>We sincerely apologize for the mistake.</strong> There has been a change in your shift assignment.</p>

    <p>You have now been reassigned to the <strong>{{ $type }}</strong> shift.</p>

    <p><strong>Updated Schedule details:</strong></p>
    <p>Start Date: {{ $start_date }}</p>
    <p>End Date: {{ $end_date }}</p>

    <p><strong>New Shift Schedule:</strong></p>
    <pre>{{ $scheduleDetails }}</pre>

    <p>Please take note of this update, and feel free to reach out to the Human Resources department if you have any concerns.</p>

    <p>Thank you for your understanding.</p>

    <p>Best regards,</p>
    <p>PT. Timur Jaya Indosteel</p>
</body>
</html>
