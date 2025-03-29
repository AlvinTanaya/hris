<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Transfer Notification</title>
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
            background-color: #3b82f6;
            color: white;
            padding: 20px 30px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .email-content {
            padding: 30px;
        }

        .message {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .details-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table tr {
            border-bottom: 1px solid #dbeafe;
        }

        .details-table tr:last-child {
            border-bottom: none;
        }

        .details-table th {
            padding: 10px 5px;
            text-align: left;
            width: 35%;
            font-weight: 600;
            color: #1e40af;
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
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #2563eb;
        }

        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .company-details {
            margin-top: 5px;
            font-weight: 600;
            color: #3b82f6;
        }

        .email-footer {
            padding: 20px 30px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
            text-align: center;
            background-color: #f9fafb;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>
                @if($isHR)
                Employee Transfer Notification
                @else
                Your Employment Status Update
                @endif
            </h1>
        </div>

        <div class="email-content">
            <p class="message">
                @if($isHR)
                Dear HR Team,
                @else
                Dear {{ $user->name }},
                @endif
            </p>

            <div class="details-box">
                @if($isHR)
                <p>There has been a change regarding employee {{ $user->name }} (ID: {{ $user->employee_id }}):</p>
                @else
                <p>We are writing to inform you about an important change in your employment details:</p>
                @endif

                <table class="details-table">
                    @if($transferType == "Penetapan")
                    <tr>
                        <th>Status Update:</th>
                        <td>
                            @if($isHR)
                            Employee status changed to <strong>Permanent</strong>
                            @else
                            Congratulations! Your status is now <strong>Permanent Employee</strong>
                            @endif
                        </td>
                    </tr>
                    @elseif($transferType == "Resign")
                    <tr>
                        <th>Status Update:</th>
                        <td>
                            @if($isHR)
                            Employee status changed to <strong>Inactive (Resigned)</strong>
                            @else
                            Your employment status is now <strong>Inactive</strong>
                            @endif
                        </td>
                    </tr>
                    @else
                    <tr>
                        <th>Previous Position:</th>
                        <td>{{ $oldPosition }} ({{ $oldDepartment }})</td>
                    </tr>
                    <tr>
                        <th>New Position:</th>
                        <td>{{ $newPosition }} ({{ $newDepartment }})</td>
                    </tr>
                    @endif
                    @if($isHR && $reason)
                    <tr>
                        <th>Reason:</th>
                        <td>{{ $reason }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Effective Date:</th>
                        <td>{{ now()->format('F j, Y') }}</td>
                    </tr>
                </table>
            </div>

            @if(!$isHR)
            <p class="message">
                If you have any questions about this change, please contact the HR department.
            </p>
            @endif

            <div class="button-container">
                <a href="{{ route('welcome') }}" class="button">
                    @if($isHR)
                    View Employee Profile
                    @else
                    View Your Profile
                    @endif
                </a>
            </div>

            <div class="signature">
                <p>Best regards,</p>
                <div class="company-details">
                    <p>Human Resources Department</p>
                    <p>{{ config('app.name') }}</p>
                </div>
            </div>
        </div>

        <div class="email-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>