<!-- labor-demand-revised.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labor Demand Request Needs Revision</title>
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
            color: #3b82f6;
        }

        .details-box {
            background-color: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        .details-box h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #3b82f6;
            font-size: 16px;
        }

        .details-box ul {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }

        .details-box li {
            padding: 5px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .details-box li:last-child {
            border-bottom: none;
        }

        .reason-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        .reason-box h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #3b82f6;
            font-size: 16px;
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

        .email-footer {
            padding: 20px 30px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Labor Demand Request Needs Revision</h1>
        </div>

        <div class="email-content">
            <p class="greeting">Greetings,</p>

            <p class="message">
                The labor demand request with ID: <strong>{{ $demand->recruitment_demand_id }}</strong> requires revision as requested by the General Manager.
            </p>

            <div class="details-box">
                <h3>Request Details:</h3>
                <ul>
                    <li><strong>Position:</strong> {{ $demand->position }}</li>
                    <li><strong>Department:</strong> {{ $demand->department }}</li>
                    <li><strong>Quantity Needed:</strong> {{ $demand->qty_needed }}</li>
                </ul>
            </div>

            <div class="reason-box">
                <h3>Revision Instructions:</h3>
                <p style="margin: 0;">{{ $demand->response_reason }}</p>
            </div>

            <p class="message">
                Please review the feedback and make the necessary revisions as soon as possible. Once updated, the request will be reevaluated for approval.
            </p>

            <div class="button-container">
                <a href="{{ $url }}" class="button">Review and Update Request</a>
            </div>

            <p>
                Thank you,<br>
                <strong>{{ config('app.name') }}</strong>
            </p>
        </div>

        <div class="email-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>