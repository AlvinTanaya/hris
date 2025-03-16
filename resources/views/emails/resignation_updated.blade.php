<!DOCTYPE html>
<html>
<head>
    <title>Resignation Request Updated</title>
</head>
<body>
    <p>Dear {{ $user->name }},</p>

    <p>Your resignation request has been updated. Please review the updated details:</p>

    <ul>
        <li><strong>Resignation Type:</strong> {{ $resignRequest->resign_type }}</li>
        <li><strong>Resignation Date:</strong> {{ $resignRequest->resign_date }}</li>
        <li><strong>Reason:</strong> {{ $resignRequest->resign_reason }}</li>
    </ul>

    <p>Management will review your request and provide a decision soon.</p>

    <p>Best regards,</p>
    <p>PT. Timur Jaya Indosteel</p>
</body>
</html>
