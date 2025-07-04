# Toast Notification System Guide

## Overview

The leave portal uses a robust toast notification system that displays temporary messages to users. The system supports four types of notifications: success, error, warning, and info.

## How It Works

### Backend (Laravel)

1. **Flash Messages**: Controllers use Laravel's flash session to send messages to the frontend:
   ```php
   return redirect()->back()->with('success', 'Operation completed successfully!');
   return redirect()->back()->with('error', 'Something went wrong!');
   return redirect()->back()->with('warning', 'Please check your input!');
   return redirect()->back()->with('info', 'Here is some information!');
   ```

2. **Middleware Processing**: The `HandleInertiaRequests` middleware automatically:
   - Detects flash messages in the session
   - Converts them to the proper format for Inertia.js
   - Clears them from the session to prevent duplicate displays

### Frontend (Vue.js)

1. **Alert Component**: The `Alert.vue` component automatically:
   - Watches for alert props from the backend
   - Displays both a visual alert and a toast notification
   - Handles all four message types

2. **Toast Service**: The `ToastService` provides:
   - Reliable toast notifications with fallback to console
   - Consistent styling and behavior
   - Error handling for edge cases

## Usage Examples

### Backend Controllers

```php
// Success message
return redirect()->back()->with('success', 'Leave application submitted successfully!');

// Error message
return redirect()->back()->with('error', 'Failed to submit leave application.');

// Warning message
return redirect()->back()->with('warning', 'Please check your leave balance.');

// Info message
return redirect()->back()->with('info', 'Your request is being processed.');
```

### Frontend Components

```typescript
// Using ToastService directly
import { ToastService } from '@/services/toast';

ToastService.success('Operation completed!');
ToastService.error('Something went wrong!');
ToastService.warning('Please check your input!');
ToastService.info('Here is some information!');
```

### In Vue Components with Global $toast

```typescript
// In setup script
const { proxy } = getCurrentInstance();

// Usage
proxy.$toast.success('Success message');
proxy.$toast.error('Error message');
proxy.$toast.warning('Warning message');
proxy.$toast.info('Info message');
```

## Test Routes

The system includes test routes to verify functionality:

- `/test-alert` - Tests success messages
- `/test-alert-error` - Tests error messages  
- `/test-alert-warning` - Tests warning messages
- `/test-alert-info` - Tests info messages

## Features

### Automatic Display
- Flash messages from backend are automatically converted to toast notifications
- Visual alerts are also displayed for immediate feedback
- Messages are cleared after display to prevent duplicates

### Error Handling
- Fallback to console logging if toast system is unavailable
- Graceful degradation ensures messages are never lost
- Comprehensive error catching and logging

### Consistent Styling
- All toast notifications use consistent positioning (top-right)
- Appropriate timeouts for different message types
- Responsive design that works on all screen sizes

### Accessibility
- Proper ARIA labels and screen reader support
- Keyboard navigation support
- High contrast colors for different message types

## Troubleshooting

### Toast Not Showing
1. Check browser console for errors
2. Verify the toast plugin is properly initialized
3. Ensure the Alert component is included in your layout
4. Check that flash messages are being set correctly in the backend

### Duplicate Messages
1. Verify the middleware is clearing flash messages after sharing
2. Check for multiple Alert components in the DOM
3. Ensure proper route redirects are being used

### Styling Issues
1. Verify vue-toastification CSS is properly imported
2. Check for CSS conflicts with other components
3. Ensure the toast container is properly positioned

## Best Practices

1. **Use Appropriate Message Types**:
   - `success` for completed operations
   - `error` for failures and exceptions
   - `warning` for user input issues
   - `info` for general information

2. **Keep Messages Concise**: Toast notifications should be brief and actionable

3. **Avoid Overuse**: Don't show toasts for every minor action

4. **Test All Scenarios**: Ensure error handling works in all edge cases

5. **Consistent Language**: Use consistent terminology across all messages 