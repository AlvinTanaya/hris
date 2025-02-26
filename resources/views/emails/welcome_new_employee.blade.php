<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Our Company</title>
</head>
<body>
    <p>Dear {{ $employee->name }},</p>
    <p>Congratulations! You have been added as an employee in our company.</p>
    <p>Your joining date is: <strong>{{ $employee->join_date }}</strong>.</p>
    <p>Please log in to the system and complete your profile if needed.</p>
    <p>We are excited to have you on board!</p>
    <p>Best regards,</p>
    <p>HR Team</p>
</body>
</html>
