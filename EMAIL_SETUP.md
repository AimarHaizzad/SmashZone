# SmashZone Email Notification System Setup Guide

## Overview
SmashZone now includes a comprehensive email notification system that automatically sends emails for various events in your badminton court booking system.

## Email Notifications Implemented

### 1. Welcome Email
- **Trigger**: When a new user registers
- **Content**: Welcome message, platform features, getting started guide
- **Template**: `WelcomeEmail.php`

### 2. Booking Confirmation
- **Trigger**: When a booking is successfully created
- **Content**: Booking details, court information, time slots, total price
- **Template**: `BookingConfirmation.php`

### 3. Payment Confirmation
- **Trigger**: When a payment is successfully processed
- **Content**: Payment details, booking information, confirmation
- **Template**: `PaymentConfirmation.php`

### 4. Booking Reminder
- **Trigger**: 24 hours before scheduled booking (automated)
- **Content**: Reminder details, important information, preparation tips
- **Template**: `BookingReminder.php`

### 5. Booking Cancellation
- **Trigger**: When a booking is cancelled
- **Content**: Cancellation details, refund information, rebooking options
- **Template**: `BookingCancellation.php`

## Email Configuration

### 1. Environment Variables
Add these to your `.env` file:

```env
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="SmashZone"
```

### 2. Gmail Setup (Recommended)
1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password:
   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate password for "Mail"
3. Use the generated password in `MAIL_PASSWORD`

### 3. Alternative Email Providers

#### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-mailgun-secret
```

#### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
```

#### Amazon SES
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
```

## Testing Email Notifications

### 1. Test Routes
Visit these URLs to test email functionality (requires authentication):

- `/test-email-notifications` - Setup test data and view available tests
- `/test-email/welcome` - Send welcome email
- `/test-email/booking-confirmation` - Send booking confirmation
- `/test-email/payment-confirmation` - Send payment confirmation
- `/test-email/booking-reminder` - Send booking reminder
- `/test-email/booking-cancellation` - Send booking cancellation

### 2. Manual Testing
```bash
# Test email configuration
php artisan tinker
Mail::raw('Test email from SmashZone', function($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email');
});
```

## Automated Reminders

### 1. Setup Cron Job
Add this to your server's crontab to send daily booking reminders:

```bash
# Run every day at 9:00 AM
0 9 * * * cd /path/to/smashzone && php artisan bookings:send-reminders
```

### 2. Manual Reminder Sending
```bash
php artisan bookings:send-reminders
```

## Customization

### 1. Email Templates
Custom email templates are located at:
- `resources/views/vendor/mail/html/themes/default/message.blade.php`

### 2. Notification Classes
All notification classes are in:
- `app/Notifications/`

### 3. Customizing Email Content
Edit the notification classes to modify email content, subject lines, and formatting.

## Troubleshooting

### 1. Common Issues

#### Emails not sending
- Check mail configuration in `.env`
- Verify SMTP credentials
- Check server logs: `storage/logs/laravel.log`

#### Emails going to spam
- Configure SPF and DKIM records
- Use a reputable email provider
- Avoid spam trigger words

#### Queue issues
- Ensure queue worker is running: `php artisan queue:work`
- Check queue configuration in `config/queue.php`

### 2. Debug Commands
```bash
# Test mail configuration
php artisan config:cache
php artisan route:cache

# Clear caches if needed
php artisan config:clear
php artisan route:clear
```

## Security Considerations

1. **Email Verification**: Consider implementing email verification for new registrations
2. **Rate Limiting**: Implement rate limiting for email sending
3. **Privacy**: Ensure compliance with data protection regulations
4. **Unsubscribe**: Consider adding unsubscribe links for marketing emails

## Performance Optimization

1. **Queue Jobs**: All emails are queued for better performance
2. **Batch Processing**: Consider batching multiple emails
3. **Caching**: Cache email templates for faster rendering
4. **Monitoring**: Monitor email delivery rates and bounce rates

## Next Steps

1. Configure your email provider settings
2. Test all email notifications
3. Set up automated reminders
4. Monitor email delivery and user engagement
5. Customize email templates to match your branding

For support or questions, please refer to the Laravel documentation or contact the development team.
