<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Off Assignment Removed</title>
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

        .next-steps {
            background-color: #f8fafc;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 4px;
        }

        .next-steps h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #4b5563;
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


    <!-- TIME OFF ASSIGNMENT REMOVED TEMPLATE -->
    <div class="email-container" style="margin-top: 50px;">
        <div class="email-header" style="background-color: #dc3545;">
            <h1>Time Off Assignment Removed</h1>
        </div>

        <div class="email-content">
            <p class="greeting">Dear {{ $data['employee_name'] }},</p>

            <p class="message">
                This is to inform you that your <strong>{{ $data['time_off_name'] }}</strong> assignment has been <strong>removed</strong> from your account.
            </p>

            <div class="details-box" style="border-left: 4px solid #dc3545;">
                <h3 style="color: #dc3545;">Removal Details:</h3>
                <table class="details-table">
                    <tr>
                        <th>Type Removed:</th>
                        <td>{{ $data['time_off_name'] }}</td>
                    </tr>
                    <tr>
                        <th>Date Removed:</th>
                        <td>{{ date('F d, Y') }}</td>
                    </tr>
                </table>
            </div>

            <div class="next-steps">
                <h3>What's Next?</h3>
                <p>If you have any questions regarding this removal, please contact the HR department for clarification.</p>
            </div>

            <div class="button-container">
                <a href="{{ route('welcome') }}" class="button" style="background-color: #dc3545;">View Time Off Status</a>
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