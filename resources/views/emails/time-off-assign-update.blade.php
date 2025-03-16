<!DOCTYPE html>
<html>
<head>
    <title>Time Off Balance Updated</title>
</head>
<body>
    <p>Dear {{ $data['employee_name'] }},</p>
    
    <p>Your {{ $data['time_off_name'] }} balance has been updated from {{ $data['old_balance'] }} to {{ $data['new_balance'] }} days.</p>
    
    <p>Best regards,<br>
    HR Department<br>
    PT. Timur Jaya Indosteel</p>
</body>
</html>