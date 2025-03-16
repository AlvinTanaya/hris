<!DOCTYPE html>
<html>
<head>
    <title>Overtime Request Cancelled</title>
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
        .cancelled {
            color: #fd7e14;
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
            <h2>Overtime Request <span class="cancelled">Cancelled</span></h2>
        </div>
        
        <div class="content">
            <p>Dear HR Department,</p>
            
            <p>An overtime request from <strong>{{ $employee->name }}</strong> has been <strong class="cancelled">cancelled</strong> by the employee.</p>
            
            <h3>Cancelled Overtime Details:</h3>
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
                <tr>
                    <th>Original Reason</th>
                    <td>{{ $overtime->reason }}</td>
                </tr>
            </table>
            
            <p>No further action is required on this request.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message from PT. Timur Jaya Indosteel HR System.</p>
        </div>
    </div>
</body>
</html>