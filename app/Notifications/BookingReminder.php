<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['brevo'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $court = $this->booking->court;
        $startTime = \Carbon\Carbon::parse($this->booking->start_time)->format('g:i A');
        $endTime = \Carbon\Carbon::parse($this->booking->end_time)->format('g:i A');
        $date = \Carbon\Carbon::parse($this->booking->date)->format('l, F j, Y');

        return (new MailMessage)
            ->subject('Reminder: Your Badminton Booking Tomorrow - ' . $court->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a friendly reminder about your badminton court booking tomorrow.')
            ->line('**Booking Details:**')
            ->line('🏸 **Court:** ' . $court->name)
            ->line('📅 **Date:** ' . $date)
            ->line('⏰ **Time:** ' . $startTime . ' - ' . $endTime)
            ->line('📋 **Booking ID:** #' . $this->booking->id)
            ->action('View Booking Details', url('/bookings/' . $this->booking->id))
            ->line('**Important Reminders:**')
            ->line('• Please arrive 10 minutes before your scheduled time')
            ->line('• Bring your own racket and shuttlecocks (or rent from us)')
            ->line('• Wear appropriate sports shoes')
            ->line('• Water and towels are available at the facility')
            ->line('If you need to cancel or modify your booking, please contact us as soon as possible.')
            ->line('We look forward to seeing you!')
            ->salutation('Best regards, The SmashZone Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'court_name' => $this->booking->court->name,
            'date' => $this->booking->date,
            'start_time' => $this->booking->start_time,
            'end_time' => $this->booking->end_time,
        ];
    }
}
