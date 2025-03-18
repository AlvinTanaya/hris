<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Off Request Submitted</title>
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

        .next-steps {
            background-color: #dbeafe;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 4px;
            border: 1px solid #bfdbfe;
        }

        .next-steps h3 {
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
            <h1>New Time Off Request Submitted</h1>
        </div>

        <div class="email-content">
            <p class="greeting">Dear HR Team Member,</p>

            <p class="message">
                A new time off request has been submitted by <strong>{{ $user->name }}</strong>. Please review the request details below.
            </p>

            <div class="details-box">
                <h3>Request Details:</h3>
                <table class="details-table">
                    <tr>
                        <th>Employee:</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Employee ID:</th>
                        <td>{{ $user->employee_id ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Department:</th>
                        <td>{{ $user->department ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Time Off Type:</th>
                        <td>{{ $policy->time_off_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Time Off Description:</th>
                        <td>{{ $policy->time_off_description ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Start Date:</th>
                        <td>{{ date('F d, Y', strtotime($timeOffRequest->start_date)) }}</td>
                    </tr>
                    <tr>
                        <th>End Date:</th>
                        <td>{{ date('F d, Y', strtotime($timeOffRequest->end_date)) }}</td>
                    </tr>
                    <tr>
                        <th>Total Days:</th>
                        <td>{{ \Carbon\Carbon::parse($timeOffRequest->start_date)->diffInDays(\Carbon\Carbon::parse($timeOffRequest->end_date)) + 1 }} days</td>
                    </tr>
                    <tr>
                        <th>Reason:</th>
                        <td>{{ $timeOffRequest->reason }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td><span style="color: #f59e0b; font-weight: bold;">{{ ucfirst($timeOffRequest->status) }}</span></td>
                    </tr>
                    <tr>
                        <th>Date Submitted:</th>
                        <td>{{ date('F d, Y H:i', strtotime($timeOffRequest->created_at)) }}</td>
                    </tr>
                </table>
            </div>

            <div class="next-steps">
                <h3>Action Required</h3>
                <p>Please review this request and take appropriate action. Check the employee's remaining balance before approval. The employee will be notified once the request has been processed.</p>
            </div>

            <div class="button-container">
                <a href="{{ route('welcome') }}" class="button">Review Request</a>
            </div>

            <div class="signature">
                <p>
                    Best Regards,<br>
                    <strong>{{ config('app.name') }}</strong>
                </p>
            </div>
        </div>

        <div class="email-footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} PT. Timur Jaya Indosteel. All rights reserved.</p>
        </div>
    </div>
</body>

</html>