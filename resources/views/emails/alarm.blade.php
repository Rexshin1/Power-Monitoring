<!DOCTYPE html>
<html>
<head>
    <title>Power Monitoring Alarm</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        .header { background-color: #FF3636; color: white; padding: 10px 20px; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .footer { font-size: 12px; color: #888; text-align: center; margin-top: 20px; }
        .alarm-type { font-size: 18px; font-weight: bold; color: #FF3636; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>⚠️ Power Alarm Triggered</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>The Power Monitoring System has detected an anomaly:</p>
            
            <p class="alarm-type">{{ $data['type'] }}</p>
            <p><strong>Message:</strong> {{ $data['message'] }}</p>
            <p><strong>Time:</strong> {{ $data['time'] }}</p>
            
            <br>
            <p>Please check the dashboard for more details.</p>
            <a href="{{ url('/') }}" style="background-color: #FF3636; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Go to Dashboard</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Power Monitoring System
        </div>
    </div>
</body>
</html>
