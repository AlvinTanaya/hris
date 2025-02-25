<!DOCTYPE html>
<html>
<head>
    <title>New Job Applicant Notification</title>
</head>
<body>
    <h2>New Job Application Received</h2>
    <p>Dear HR Team,</p>

    <p>A new job application has been submitted. Please find the details below:</p>

    <ul>
        <li><strong>Applicant Name:</strong> {{ $query->name }}</li>
        <li><strong>Email:</strong> {{ $query->email }}</li>
        <li><strong>Labor Demand ID:</strong> {{ $query->labor_demand_id }}</li>
    </ul>

    <p>Please review the application in the system.</p>

    <p>Best Regards,</p>
    <p>Recruitment System</p>
</body>
</html>
