<!DOCTYPE html>
<html>
<head>
    <title>New Employee Joined</title>
</head>
<body>
    <p>Dear Team,</p>
    <p>We are pleased to announce that <strong>{{ $employee->name }}</strong> has joined our company as <strong>{{ $employee->position }}</strong> in the <strong>{{ $employee->department }}</strong> department.</p>
    <p>Let's welcome them and support them in their journey with us.</p>
    <p>Best regards,</p>
    <p>HR Team</p>
</body>
</html>
