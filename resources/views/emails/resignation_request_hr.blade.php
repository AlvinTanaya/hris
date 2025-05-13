<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Resignation Request</title>
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

        .highlight {
            color: #3b82f6;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>New Resignation Request</h1>
        </div>
        <div class="email-content">
            <div class="message">
                <p>Dear HR Personnel,</p>
                <p>A new resignation request has been submitted and requires your attention. Please review the details below:</p>
            </div>

            <div class="details-box">
                <p><strong>Employee Name:</strong> {{ $user->name }}</p>
                <p><strong>Employee ID:</strong> {{ $user->employee_id ?? $user->id }}</p>
                <p><strong>Department:</strong> {{ $user->department->department ?? 'N/A' }}</p>
                <p><strong>Position:</strong> {{ $user->position->position ?? 'N/A' }}</p>
                <p><strong>Resignation Type:</strong> {{ $resignation->resign_type }}</p>
                <p><strong>Requested Resignation Date:</strong> {{ date('F d, Y', strtotime($resignation->resign_date)) }}</p>
                <p><strong>Reason for Resignation:</strong> {{ $resignation->resign_reason }}</p>
                @if($resignation->file_path)
                <p><strong>Supporting Document:</strong> Attached</p>
                @endif
            </div>

            <p>This request is now <span class="highlight">pending approval</span>. Please review the request and take appropriate action at your earliest convenience.</p>

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