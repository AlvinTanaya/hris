<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overtime Request Declined</title>
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
            background-color: #ef4444;
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
            color: #ef4444;
        }

        .details-box {
            background-color: #f8fafc;
            border-left: 4px solid #ef4444;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        .details-box h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #ef4444;
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
            <h1>Overtime Request Declined</h1>
        </div>

        <div class="email-content">
            <p class="greeting">Hello, {{ $employee->name }}</p>

            <p class="message">
                We regret to inform you that your overtime request has been <strong>declined</strong>.
            </p>

            <div class="details-box">
                <h3>Declined Overtime Details:</h3>
                <table class="details-table">
                    <tr>
                        <th>Date:</th>
                        <td>{{ $overtime->date }}</td>
                    </tr>
                    <tr>
                        <th>Time Period:</th>
                        <td>{{ $overtime->start_time }} - {{ $overtime->end_time }}</td>
                    </tr>
                    <tr>
                        <th>Duration:</th>
                        <td>{{ $overtime->total_hours }} hours</td>
                    </tr>
                    <tr>
                        <th>Type:</th>
                        <td>{{ $overtime->overtime_type }}</td>
                    </tr>
                    <tr>
                        <th>Reason for Decline:</th>
                        <td>{{ $overtime->declined_reason }}</td>
                    </tr>
                </table>
            </div>

            <p class="message">
                If you have any questions or would like to discuss this decision further, please contact your supervisor or the HR department.
            </p>

            <div class="button-container">
                <a href="{{ route('welcome') }}" class="button">Go to Website</a>
            </div>

            <div class="signature">
                <p>
                    Thank you for your understanding,<br>
                    <strong>{{ config('app.name') }}</strong>
                </p>
            </div>
        </div>

        <div class="email-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>