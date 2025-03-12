<!DOCTYPE html>
<html>

<head>
    <title>Resignation Request Declined</title>
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
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .header {
            background-color: #dc3545;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            padding: 20px;
        }

        .reason {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #dc3545;
            margin: 15px 0;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Resignation Request Declined</h2>
        </div>
        <div class="content">
            <p>Dear {{ $employee->name }},</p>

            <p>This email is to inform you that your resignation request has been <strong>declined</strong>.</p>

            <p>
                <strong>Details:</strong><br>
                Resignation Type: {{ $request->resign_type }}<br>
                Requested Date: {{ \Carbon\Carbon::parse($request->resign_date)->format('d M Y') }}
            </p>

            <div class="reason">
                <strong>Reason for Decline:</strong>
                <p>{{ $reason }}</p>
            </div>

            <p>If you would like to discuss this matter further or have any questions, please contact your manager or the HR department.</p>

            <p>
                Regards,<br>
                {{ $decliner }}<br>
                HR Department
            </p>
        </div>
        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>

</html>