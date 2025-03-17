<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulations!</title>
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
            font-size: 26px;
            font-weight: 600;
        }

        .email-content {
            padding: 30px;
        }

        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #10b981;
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 25px;
            font-size: 16px;
        }

        .message strong {
            color: #10b981;
        }

        .details-box {
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        .congrats-banner {
            background-color: #f0fdf4;
            padding: 20px;
            text-align: center;
            margin-bottom: 25px;
            border-radius: 6px;
        }

        .congrats-banner img {
            max-width: 100px;
            margin-bottom: 10px;
        }

        .congrats-banner h2 {
            color: #10b981;
            margin: 0;
            font-size: 24px;
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
            margin-top: 10px;
            font-size: 14px;
            color: #6b7280;
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
            <h1>Congratulations!</h1>
        </div>

        <div class="email-content">
            <div class="congrats-banner">
                <svg width="100" height="100" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#10b981" stroke-width="2" />
                    <path d="M8 12L11 15L16 9" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <h2>Welcome to the Team!</h2>
            </div>

            <p class="greeting">Dear {{ $name }},</p>

            <p class="message">
                We are delighted to inform you that you have officially been accepted as <strong>{{ $position }}</strong> in the <strong>{{ $department }}</strong> department at <strong>PT Timur Jaya Indstell</strong>.
            </p>

            <div class="details-box">
                <p><strong>Your joining date:</strong> {{ $joinDate }}</p>
                <p style="margin-bottom: 0;">Please be prepared and report accordingly. Our team will provide you with further details about your first day soon.</p>
            </div>

            <p class="message">
                We are excited to have you join our team and look forward to the valuable contributions you will make to our organization.
            </p>

            <div class="button-container">
                <a href="{{ route('welcome') }}" class="button">Go to Website</a>
            </div>

            <div class="signature">
                <p>Best regards,</p>
                <div class="company-details">
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