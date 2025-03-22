<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warning Letter Notification</title>
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
            background-color: #dc3545;
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
            color: #dc3545;
        }

        .details-box {
            background-color: #f8fafc;
            border-left: 4px solid #dc3545;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        .details-box h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #dc3545;
            font-size: 16px;
        }

        .warning-box {
            background-color: #fff3f3;
            border: 1px solid #dc3545;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 4px;
        }

        .warning-box h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #dc3545;
            font-size: 16px;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #c82333;
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
            <h1>{{ $isUpdate ? 'Warning Letter Update' : 'Warning Letter Notification' }}</h1>
        </div>

        <div class="email-content">
            <p class="greeting">Dear {{ $user->name }},</p>

            <div class="warning-box">
                @if ($isUpdate)
                    <h3>Warning Letter Updated</h3>
                    @if ($oldType != $type)
                        <p>Your warning letter has been <strong>updated from {{ $oldType }} to {{ $type }} #{{ $typeCount }}</strong>.</p>
                    @else
                        <p>Your <strong>{{ $type }} warning letter #{{ $typeCount }}</strong> has been updated.</p>
                    @endif
                @else
                    <h3>Warning Letter Issued</h3>
                    @if ($typeCount == 1)
                        <p>This is your <strong>first {{ $type }} warning letter</strong>.</p>
                    @else
                        <p>This is your <strong>{{ $type }} warning letter #{{ $typeCount }}</strong>.</p>
                    @endif
                @endif
                <p><strong>Reason:</strong> {{ $reason }}</p>
            </div>

            @if ($isTermination ?? false)
                <div class="details-box">
                    <h3>Important Notice</h3>
                    <p>
                        You have received an <strong>SP3 (Final Warning)</strong>. As per company policy, your employment status has been changed to inactive.
                    </p>
                </div>
            @endif

            <div class="details-box">
                <h3>{{ $isUpdate ? 'Updated By' : 'Issued By' }}</h3>
                <p>This warning letter was {{ $isUpdate ? 'updated' : 'issued' }} by: <strong>{{ $maker->name }}</strong>.</p>
            </div>

            <p class="message">
                If you have any questions or would like to discuss this matter further, please contact the HR Department.
            </p>

            <div class="button-container">
                <a href="{{ route('welcome') }}" class="button">Visit Our Website</a>
            </div>

            <div class="signature">
                <p>
                    Best regards,<br>
                    <strong>{{ config('app.name') }}</strong>
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