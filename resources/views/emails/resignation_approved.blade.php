<!DOCTYPE html>
<html>

<head>
    <title>Resignation Request Approved</title>
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
            background-color: #28a745;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            padding: 20px;
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
            <h2>Resignation Request Approved</h2>
        </div>
        <div class="content">
            <p>Dear {{ $employee->name }},</p>

            <p>This email is to confirm that your resignation request has been <strong>approved</strong>.</p>

            <p>
                <strong>Details:</strong><br>
                Resignation Type: {{ $request->resign_type }}<br>
                Effective Date: {{ \Carbon\Carbon::parse($request->resign_date)->format('d M Y') }}
            </p>

            <p>Your employment status has been updated to Inactive in our system. Please ensure that you complete all pending work and hand over your responsibilities before your last working day.</p>

            <p>The Human Resources department will be in touch with you regarding the exit process and any final settlements.</p>

            <p>If you have any questions or need further assistance, please don't hesitate to contact the HR department.</p>

            <p>We wish you all the best in your future endeavors.</p>

            <p>
                Regards,<br>
                {{ $approver }}<br>
                HR Department
            </p>
        </div>
        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>

</html>