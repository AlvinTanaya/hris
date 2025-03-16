
<!DOCTYPE html>
<html>
<head>
    <title>Overtime Request Approved</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 0.8em;
        }
        .approved {
            color: #28a745;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td, table th {
            padding: 8px;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Overtime Request <span class="approved">Approved</span></h2>
        </div>
        
        <div class="content">
            <p>Dear Employee,</p>
            
            <p>Your overtime request has been <strong class="approved">approved</strong>.</p>
            
            <h3>Overtime Details:</h3>
            <table>
                <tr>
                    <th>Date</th>
                    <td>{{ $overtime->date }}</td>
                </tr>
                <tr>
                    <th>Time</th>
                    <td>{{ $overtime->start_time }} - {{ $overtime->end_time }}</td>
                </tr>
                <tr>
                    <th>Total Hours</th>
                    <td>{{ $overtime->total_hours }}</td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td>{{ $overtime->overtime_type }}</td>
                </tr>
            </table>
            
            <p>Please ensure you log your overtime hours according to company policy.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message from PT. Timur Jaya Indosteel HR System.</p>
        </div>
    </div>
</body>
</html>