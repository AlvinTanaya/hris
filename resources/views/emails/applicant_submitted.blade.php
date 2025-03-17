<!-- application-submitted.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted</title>
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
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 15px;
            font-size: 16px;
        }
        .confirmation-box {
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 0 4px 4px 0;
        }
        .confirmation-box svg {
            display: block;
            width: 40px;
            height: 40px;
            margin: 0 auto 10px;
        }
        .confirmation-box h3 {
            text-align: center;
            color: #10b981;
            margin-top: 0;
        }
        .next-steps {
            margin-top: 25px;
            padding: 15px 20px;
            background-color: #f8fafc;
            border-radius: 6px;
        }
        .next-steps h3 {
            margin-top: 0;
            color: #4b5563;
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
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Application Submitted</h1>
        </div>
        
        <div class="email-content">
            <p class="greeting">Hello, {{ $applicant->name }}!</p>
            
            <div class="confirmation-box">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#10b981" stroke-width="2"/>
                    <path d="M8 12L11 15L16 9" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h3>Thank you for submitting your job application</h3>
            </div>
            
            <p class="message">
                We have received your application and it is currently under review by our recruitment team.
            </p>
            
            <div class="next-steps">
                <h3>What happens next?</h3>
                <ul>
                    <li>Stay tuned to get an email from our HRD for an interview if you qualify</li>
                    <li>Please check your email regularly for updates from us</li>
                    <li>If selected, you will be contacted within the next few weeks</li>
                </ul>
            </div>
            
            <div class="signature">
                <p>Best Regards,</p>
                <p class="company-details">{{ config('app.name') }}</p>
            </div>
        </div>
        
        <div class="email-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>