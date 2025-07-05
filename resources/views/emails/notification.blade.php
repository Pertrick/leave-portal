<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notification' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px 20px;
        }
        
        .notification-icon {
            font-size: 48px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .notification-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .notification-message {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 25px;
            line-height: 1.7;
        }
        
        .notification-details {
            background-color: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .detail-item:last-child {
            margin-bottom: 0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #2d3748;
        }
        
        .detail-value {
            color: #4a5568;
        }
        
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s ease;
        }
        
        .action-button:hover {
            transform: translateY(-2px);
        }
        
        .footer {
            background-color: #f7fafc;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer p {
            font-size: 14px;
            color: #718096;
            margin-bottom: 10px;
        }
        
        .footer-links {
            margin-top: 15px;
        }
        
        .footer-links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .priority-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        
        .priority-high {
            background-color: #fed7d7;
            color: #c53030;
        }
        
        .priority-normal {
            background-color: #c6f6d5;
            color: #2f855a;
        }
        
        .priority-low {
            background-color: #bee3f8;
            color: #2b6cb0;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 4px;
            }
            
            .header, .content, .footer {
                padding: 20px 15px;
            }
            
            .notification-title {
                font-size: 18px;
            }
            
            .notification-message {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Leave Portal</h1>
            <p>Your HR Management System</p>
        </div>
        
        <div class="content">
            @if(isset($priority) && $priority !== 'normal')
                <div class="priority-badge priority-{{ $priority }}">
                    {{ ucfirst($priority) }} Priority
                </div>
            @endif
            
            <div class="notification-icon">
                {{ $icon ?? '📢' }}
            </div>
            
            <div class="notification-title">
                {{ $title ?? 'Notification' }}
            </div>
            
            <div class="notification-message">
                {!! $message ?? '' !!}
            </div>
            
            @if(isset($details) && count($details) > 0)
                <div class="notification-details">
                    @foreach($details as $label => $value)
                        <div class="detail-item">
                            <span class="detail-label">{{ $label }}:</span>
                            <span class="detail-value">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
            
            @if(isset($actionUrl) && isset($actionText))
                <div style="text-align: center;">
                    <a href="{{ $actionUrl }}" class="action-button">
                        {{ $actionText }}
                    </a>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p>This is an automated notification from the Leave Portal system.</p>
            <p>If you have any questions, please contact your HR department.</p>
            
            <div class="footer-links">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('profile.show') }}">Profile</a>
                <a href="{{ route('leave.applications.index') }}">Leave Applications</a>
            </div>
        </div>
    </div>
</body>
</html> 