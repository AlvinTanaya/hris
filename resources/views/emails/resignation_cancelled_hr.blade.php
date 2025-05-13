<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelled Resignation Request</title>
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
            background-color: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            background-color: #10b981;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #059669;
        }

        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .company-details {
            margin-top: 5px;
            font-weight: 600;
            color: #10b981;
        }

        .email-footer {
            padding: 20px 30px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
            text-align: center;
            background-color: #f9fafb;
        }

        .highlight {
            color: #10b981;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Resignation Request Cancelled</h1>
        </div>
        <div class="email-content">
            <div class="message">
                <p>Dear HR Personnel,</p>
                <p>This is to inform you that an employee has <span class="highlight">cancelled</span> their resignation request. Here are the details of the cancelled request:</p>
            </div>

            <div class="details-box">
                <p><strong>Employee Name:</strong> {{ $resignDetails['user']->name }}</p>
                <p><strong>Employee ID:</strong> {{ $resignDetails['user']->employee_id ?? $resignDetails['user']->id }}</p>
                <p><strong>Department:</strong> {{ $resignDetails['user']->department->department ?? 'N/A' }}</p>
                <p><strong>Position:</strong> {{ $resignDetails['user']->position->position ?? 'N/A' }}</p>
                <p><strong>Original Resignation Type:</strong> {{ $resignDetails['resign_type'] }}</p>
                <p><strong>Original Resignation Date:</strong> {{ date('F d, Y', strtotime($resignDetails['resign_date'])) }}</p>
            </div>

            <p>The resignation request has been <span class="highlight">completely withdrawn</span> by the employee. No further action is required for this resignation request.</p>

            <p>The employee has decided to continue their employment with the company. Please update your records accordingly.</p>

            <div class="button-container">
                <a href="{{ route('welcome') }}" class="button">View</a>
            </div>

            <div class="signature">
                <p>Best regards,</p>
                <p><strong>{{ config('app.name') }} HR Management System</strong></p>
                <p class="company-details">{{ config('app.name') }}</p>
            </div>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>