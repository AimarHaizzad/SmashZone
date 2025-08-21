# Automatic Booking Completion Feature

## Overview
This feature automatically marks past bookings as "completed" when they've passed their scheduled end time, indicating that the games have been played.

## What It Does
- **Automatically detects** bookings that have ended (past date or past end time on current date)
- **Updates status** from "confirmed" to "completed" 
- **Runs automatically** every hour via scheduled task
- **Logs all actions** for monitoring and debugging

## New Status: "Completed"
- Added a new booking status: `completed`
- Completed bookings are shown with gray styling in the UI
- Cannot be modified once completed (read-only status)

## How It Works

### 1. Console Command
```bash
php artisan bookings:complete-past
```
This command:
- Finds all confirmed bookings that have ended
- Updates their status to "completed"
- Provides detailed output of what was processed

### 2. Automatic Scheduling
The command runs automatically every hour via Laravel's task scheduler:
- **Frequency**: Every hour
- **Logging**: Output saved to `storage/logs/booking-completion.log`
- **Overlap Protection**: Prevents multiple instances from running simultaneously

### 3. Database Changes
- Added `completed` to the status enum in the bookings table
- New migration: `2025_01_28_000000_add_completed_status_to_bookings_table.php`

## Usage

### Manual Execution
```bash
# Run the command manually
php artisan bookings:complete-past

# Check the output
tail -f storage/logs/booking-completion.log
```

### Testing
Visit `/test-complete-bookings` in your browser to manually trigger the command and see the output.

### Monitoring
Check the logs to see:
- How many bookings were completed
- Any errors that occurred
- Execution timestamps

## UI Updates
- **Dashboard**: Shows completed status with gray styling
- **Owner/Staff Views**: Display completed bookings
- **Edit Form**: Includes completed status option
- **Status Colors**:
  - `confirmed`: Blue
  - `completed`: Gray  
  - `pending`: Yellow
  - `cancelled`: Red

## Model Methods
The `Booking` model now includes helpful methods:

```php
// Check if booking is in the past
$booking->isPast()

// Check if booking is completed
$booking->isCompleted()

// Check if booking is active (confirmed and not past)
$booking->isActive()

// Query scopes
Booking::active()  // Only active bookings
Booking::past()    // Only past bookings
```

## Configuration
The scheduling is configured in `app/Console/Kernel.php`:

```php
// Run every hour
$schedule->command('bookings:complete-past')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/booking-completion.log'));
```

## Troubleshooting

### Command Not Running
1. Check if Laravel scheduler is running:
   ```bash
   crontab -e
   # Add: * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
   ```

2. Check logs:
   ```bash
   tail -f storage/logs/booking-completion.log
   ```

### Migration Issues
If you get enum errors:
```bash
php artisan migrate:rollback
php artisan migrate
```

## Benefits
- **Automatic Management**: No manual intervention needed
- **Data Accuracy**: Past bookings are properly marked
- **User Experience**: Clear status indication for completed games
- **Reporting**: Better analytics on completed vs. active bookings
- **Audit Trail**: Complete history of booking lifecycle

## Future Enhancements
- Email notifications when bookings are completed
- Integration with analytics/reporting
- Custom completion rules (e.g., weather conditions, court maintenance)
- Bulk completion for specific date ranges
