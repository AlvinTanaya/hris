<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract Expiring Notification</title>
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
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #ffeeba;
        }
        .warning {
            color: #856404;
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
        .action-required {
            background-color: #e2e3e5;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>⚠️ Employee Contract Expiring Soon</h2>
            <p class="warning">Contract will expire in {{ $expiryPeriod }}</p>
        </div>
        
        <p>Dear HR Department,</p>
        
        <p>This is an automated notification to inform you that the employment contract for the following employee will expire in {{ $expiryPeriod }}:</p>
        
        <div class="details">
            <p><strong>Employee ID:</strong> {{ $employeeId }}</p>
            <p><strong>Employee Name:</strong> {{ $name }}</p>
            <p><strong>Contract End Date:</strong> {{ $contractEndDate }}</p>
        </div>
        
        <div class="action-required">
            <p><strong>Action Required:</strong> Please review this contract and take appropriate action before the expiration date. If the employee is to continue working with the company, the contract must be renewed.</p>
        </div>
        
        <p>Regards,<br>
        HR Automated System</p>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>If you have any questions, please contact the HR department.</p>
        </div>
    </div>
</body>
</html>