<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Position Transfer Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #2563eb;
            color: white;
            padding: 20px 30px;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .email-content {
            padding: 30px;
        }

        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 25px;
        }

        .message strong {
            color: #2563eb;
        }

        .details-box {
            background-color: #f8fafc;
            border-left: 4px solid #2563eb;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        .details-box h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #2563eb;
            font-size: 16px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .details-table tr:last-child {
            border-bottom: none;
        }

        .details-table th {
            padding: 10px 5px;
            text-align: left;
            width: 35%;
            font-weight: 600;
            vertical-align: top;
        }

        .details-table td {
            padding: 10px 5px;
            text-align: left;
            vertical-align: top;
        }

        .reason-box {
            background-color: #f8fafc;
            border-left: 4px solid #2563eb;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        .reason-box h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #2563eb;
            font-size: 16px;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #1d4ed8;
        }

        .email-footer {
            padding: 20px 30px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
            text-align: center;
        }

        .signature {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Position Transfer Notification</h1>
        </div>

        <div class="email-content">
            <p class="greeting">Dear {{ $user->name }},</p>

            <p class="message">
                We are writing to inform you about a change in your employment status or position.
            </p>

            <div class="details-box">
                <h3>Transfer Details:</h3>
                <table class="details-table">
                    @if($transferType == "Penetapan")
                    <tr>
                        <th>Status Update:</th>
                        <td>Congratulations! Your employment status has been updated to <strong>Permanent Employee</strong>.</td>
                    </tr>
                    @elseif($transferType == "Resign")
                    <tr>
                        <th>Status Update:</th>
                        <td>Your employment status has been updated to <strong>Inactive</strong>.</td>
                    </tr>
                    @else
                    <tr>
                        <th>Previous Position:</th>
                        <td>{{ $oldPosition }} in {{ $oldDepartment }} Department</td>
                    </tr>
                    <tr>
                        <th>New Position:</th>
                        <td>{{ $newPosition }} in {{ $newDepartment }} Department</td>
                    </tr>
                    @endif
                </table>
            </div>

            <p class="message">
                If you have any questions or need further clarification, please feel free to contact the Human Resources Department.
            </p>

            <div class="button-container">
                <a href="{{ route('welcome') }}" class="button">Visit Our Website</a>
            </div>

            <div class="signature">
                <p>
                    Best Regards,<br>
                    <strong>HR Department</strong><br>
                    {{ config('app.name') }}
                </p>
            </div>
        </div>

        <div class="email-footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>