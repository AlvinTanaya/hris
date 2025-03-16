<!DOCTYPE html>
<html>
<head>
    <title>Time Off Assignment</title>
</head>
<body>
    <p>Dear {{ $data['employee_name'] }},</p>
    
    <p>You have been assigned {{ $data['time_off_name'] }} with a balance of {{ $data['balance'] }} days.</p>
    
    <p>Best regards,<br>
    HR Department<br>
    PT. Timur Jaya Indosteel</p>
</body>
</html>