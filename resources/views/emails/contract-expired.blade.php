<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract Expired Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8d7da;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .important {
            color: #721c24;
            font-weight: bold;
        }
        .details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .footer {
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>⚠️ URGENT: Employee Contract Has Expired</h2>
            <p class="important">Immediate action required for contract renewal</p>
        </div>
        
        <p>Dear HR Department,</p>
        
        <p>This is an automated notification to inform you that the employment contract for the following employee has expired:</p>
        
        <div class="details">
            <p><strong>Employee ID:</strong> {{ $employeeId }}</p>
            <p><strong>Employee Name:</strong> {{ $name }}</p>
            <p><strong>Contract End Date:</strong> {{ $contractEndDate }}</p>
        </div>
        
        <p>Please take immediate action to renew this contract if the employee is to continue working with the company.</p>
        
        <p>Note that this employee's status is still active in the system, which indicates they may still be working without a valid contract.</p>
        
        <p>Regards,<br>
        HR Automated System</p>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>If you have any questions, please contact the HR department.</p>
        </div>
    </div>
</body>
</html>