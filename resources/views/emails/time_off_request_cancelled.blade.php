<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Off Request Cancelled</title>
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
            background-color: #6b7280;
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
            color: #6b7280;
        }

        .details-box {
            background-color: #f8fafc;
            border-left: 4px solid #6b7280;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        .details-box h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #6b7280;
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
            background-color: #f3f4f6;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }

        .next-steps h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #6b7280;
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

        .cancelled-label {
            display: inline-block;
            background-color: #6b7280;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Time Off Request Cancelled</h1>
        </div>

        <div class="email-content">
            <p class="greeting">Dear HR Team Member,</p>

            <p class="message">
                This is to inform you that <strong>{{ $user->name }}</strong> has cancelled a time off request. The request has been removed from the system.
            </p>

            <div class="details-box">
                <h3>Cancelled Request Details:</h3>
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
                        <th>Status:</th>
                        <td><span class="cancelled-label">Cancelled</span></td>
                    </tr>
       
                    <tr>
                        <th>Date Cancelled:</th>
                        <td>{{ date('F d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="next-steps">
                <h3>Information</h3>
                <p>No action is required from your side. This request has been removed from the pending requests list. The system records have been updated accordingly.</p>
            </div>

            <div class="button-container">
                <a href="{{ route('welcome') }}" class="button">View All Requests</a>
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