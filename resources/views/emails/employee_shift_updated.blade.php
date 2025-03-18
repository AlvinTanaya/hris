<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Assignment Update</title>
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
            background-color: #f0fdf4;
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
            <h1>Shift Assignment Update</h1>
        </div>
        
        <div class="email-content">
            <p class="message">Hello {{ $name }},</p>
            <p class="message"><strong>We sincerely apologize for the mistake.</strong> There has been a change in your shift assignment.</p>
            <p class="message">You have now been reassigned to the <strong>{{ $type }}</strong> shift.</p>

            <div class="details-box">
                <p><strong>Schedule Details:</strong></p>
                <p><strong>Start Date:</strong> {{ $start_date }}</p>
                <p><strong>End Date:</strong> {{ $end_date }}</p>
                <p><strong>Shift Schedule:</strong></p>

                <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr style="background-color: #dbeafe;">
                            <th style="padding: 8px; text-align: left; border: 1px solid #bfdbfe;">Day</th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #bfdbfe;">Hour In</th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #bfdbfe;">Hour Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scheduleDetails as $schedule)
                        <tr>
                            <td style="padding: 8px; border: 1px solid #bfdbfe;">{{ $schedule['day'] }}</td>
                            <td style="padding: 8px; border: 1px solid #bfdbfe;">{{ $schedule['hour_in'] }}</td>
                            <td style="padding: 8px; border: 1px solid #bfdbfe;">{{ $schedule['hour_out'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="padding: 8px; border: 1px solid #bfdbfe; text-align: center;">No schedule details available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="message">Please take note of this update, and feel free to reach out to the Human Resources department if you have any concerns.</p>
            <p class="message">Thank you for your understanding.</p>

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