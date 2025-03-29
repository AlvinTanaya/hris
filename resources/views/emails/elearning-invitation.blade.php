<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning Course Invitation</title>
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
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>
                @if($invitationType === 'new')
                E-Learning Course Invitation
                @else
                E-Learning Course Invitation Update
                @endif
            </h1>
        </div>

        <div class="email-content">
            <p class="message">Hello, {{ $userName }}</p>

            <div class="details-box">
                @if($invitationType === 'new')
                <p>You have been invited to participate in an exciting e-learning course:</p>
                <p><strong>Course: {{ $lessonName }}</strong></p>
                <p>Course Duration: {{ $startDate }} to {{ $endDate }}</p>

                <p>We believe this course will provide valuable insights and enhance your professional skills. Please review the details and prepare for an engaging learning experience.</p>
                @else
                <p>We would like to inform you about an update to your e-learning course invitation:</p>
                <p><strong>Course: {{ $lessonName }}</strong></p>
                <p>Updated Course Duration: {{ $startDate }} to {{ $endDate }}</p>

                <p>Please note that there have been some changes to your previous invitation. Kindly review the updated course details.</p>
                @endif
            </div>

            <div class="button-container">
                <a href="{{ route('welcome') }}" class="button">View Course Details</a>
            </div>

            <div class="signature">
                <p>Best regards,</p>
                <div class="company-details">
                    <p>E-Learning Team</p>
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