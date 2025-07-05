# Notification System Documentation

## Overview

The Leave Portal includes a comprehensive notification system that sends both email and in-app notifications for various events. The system is designed to keep users informed about important actions and status changes.

## Features

### Email Notifications
- Beautiful, responsive email templates
- Priority-based styling (high, normal, low)
- Action buttons for direct navigation
- Detailed information display

### In-App Notifications
- Real-time notification bell with unread count
- Dropdown interface for viewing notifications
- Mark as read functionality
- Automatic refresh

## Notification Types

### 1. Leave Application Submitted
- **Triggered**: When a user submits a leave application
- **Recipients**: Supervisors and department heads
- **Email Template**: Professional notification with application details
- **Priority**: Normal

### 2. Leave Application Approved
- **Triggered**: When a leave application is approved
- **Recipients**: The applicant
- **Email Template**: Success notification with approval details
- **Priority**: Normal

### 3. Leave Application Rejected
- **Triggered**: When a leave application is rejected
- **Recipients**: The applicant
- **Email Template**: Rejection notification with reason
- **Priority**: High

### 4. Pending Approval Reminder
- **Triggered**: Daily scheduled reminder for pending approvals
- **Recipients**: Approvers with pending applications
- **Email Template**: Reminder with application details
- **Priority**: High

## Implementation

### Backend Components

#### 1. Base Notification Class
```php
app/Notifications/BaseNotification.php
```
- Abstract base class for all notifications
- Implements queue functionality
- Provides common methods for icon and priority handling

#### 2. Notification Service
```php
app/Services/NotificationService.php
```
- Centralized service for sending notifications
- Handles notification logic and recipient determination
- Provides methods for different notification types

#### 3. API Controller
```php
app/Http/Controllers/Api/NotificationController.php
```
- Handles in-app notification requests
- Provides endpoints for fetching and marking notifications

#### 4. Email Template
```php
resources/views/emails/notification.blade.php
```
- Beautiful, responsive email template
- Supports priority badges, action buttons, and detailed information

### Frontend Components

#### 1. Notification Bell Component
```vue
resources/js/components/NotificationBell.vue
```
- Simple notification bell with unread count
- Dropdown interface for viewing notifications
- Real-time updates

## Usage

### Sending Notifications

#### 1. Leave Application Submitted
```php
// In LeaveController
$this->notificationService->notifyLeaveSubmitted($leave);
```

#### 2. Leave Application Approved
```php
// In LeaveApprovalController
$this->notificationService->notifyLeaveApproved($leave, $approver);
```

#### 3. Leave Application Rejected
```php
// In LeaveApprovalController
$this->notificationService->notifyLeaveRejected($leave, $rejecter, $reason);
```

### Adding to Frontend

#### 1. Include in Layout
```vue
<template>
  <div class="header">
    <!-- Other header content -->
    <NotificationBell />
  </div>
</template>

<script setup>
import NotificationBell from '@/components/NotificationBell.vue'
</script>
```

## Configuration

### Email Configuration
Ensure your `.env` file has proper email configuration:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourcompany.com
MAIL_FROM_NAME="Leave Portal"
```

### Queue Configuration
For better performance, configure queues:
```env
QUEUE_CONNECTION=database
```

Run queue worker:
```bash
php artisan queue:work
```

### Scheduled Tasks
The system includes a scheduled task for sending pending approval reminders:
```bash
# Add to crontab
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Customization

### Adding New Notification Types

1. Create a new notification class extending `BaseNotification`
2. Implement `toMail()` and `toArray()` methods
3. Add notification logic to the appropriate service
4. Update the frontend component if needed

### Customizing Email Template

The email template supports the following variables:
- `title`: Notification title
- `message`: Notification message
- `icon`: Emoji icon
- `priority`: Priority level (high, normal, low)
- `details`: Array of key-value pairs for additional information
- `actionUrl`: URL for action button
- `actionText`: Text for action button

### Styling

The email template uses inline CSS for maximum compatibility. Colors and styling can be customized in the template file.

## Testing

### Manual Testing
1. Submit a leave application
2. Check email notifications
3. Check in-app notifications
4. Test approval/rejection notifications

### Automated Testing
```bash
# Test notification sending
php artisan test --filter=NotificationTest

# Test scheduled commands
php artisan notifications:send-pending-approval-reminders
```

## Troubleshooting

### Common Issues

1. **Emails not sending**
   - Check email configuration in `.env`
   - Verify queue worker is running
   - Check mail logs

2. **In-app notifications not loading**
   - Check API routes are accessible
   - Verify authentication middleware
   - Check browser console for errors

3. **Scheduled reminders not working**
   - Ensure cron job is set up
   - Check Laravel scheduler is running
   - Verify command exists and is working

### Logs
Check Laravel logs for notification errors:
```bash
tail -f storage/logs/laravel.log
```

## Security Considerations

1. **Authentication**: All notification endpoints require authentication
2. **Authorization**: Users can only access their own notifications
3. **Rate Limiting**: Consider implementing rate limiting for API endpoints
4. **Data Privacy**: Ensure sensitive information is not exposed in notifications

## Performance Optimization

1. **Queue Jobs**: All notifications are queued for better performance
2. **Database Indexing**: Ensure proper indexes on notifications table
3. **Caching**: Consider caching notification counts for better performance
4. **Batch Processing**: For large numbers of notifications, consider batch processing 