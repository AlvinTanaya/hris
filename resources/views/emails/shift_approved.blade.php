<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Change Request Approved</title>
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
            background-color: #10b981;
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
            color: #10b981;
        }

        .details-box {
            background-color: #f8fafc;
            border-left: 4px solid #10b981;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        .details-box h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #10b981;
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
            background-color: #ecfdf5;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 4px;
            border: 1px solid #d1fae5;
        }

        .next-steps h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #10b981;
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
            <h1>Shift Change Request Approved</h1>
        </div>

        <div class="email-content">
            <p class="greeting">Dear {{ $userName }},</p>

            <p class="message">
                We are pleased to inform you that your shift change request has been <strong>approved</strong>. Your schedule has been updated in our system according to your request.
            </p>

            <div class="details-box">
                <h3>New Schedule Details:</h3>
                <table class="details-table">
                    <tr>
                        <th>New Shift:</th>
                        <td><strong>{{ $newShift }}</strong></td>
                    </tr>
                    <tr>
                        <th>Start Date:</th>
                        <td>{{ $startDate }}</td>
                    </tr>
                    <tr>
                        <th>End Date:</th>
                        <td>{{ $endDate }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td><strong style="color: #10b981;">Approved</strong></td>
                    </tr>
                </table>
            </div>

            <div class="next-steps">
                <h3>Important Information</h3>
                <p>Please ensure that you are familiar with the working hours of your new shift and make any necessary personal arrangements before the start date. If you have any questions about your new schedule, please contact your supervisor.</p>
            </div>

            <p class="message">
                Thank you for your cooperation in managing this shift change. We appreciate your flexibility and commitment to our operations.
            </p>

            <div class="button-container">
                <a href="{{ route('welcome') }}" class="button">View Schedule</a>
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