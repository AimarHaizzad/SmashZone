<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingCancellation extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, $reason = null)
    {
        $this->booking = $booking;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
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

        $mailMessage = (new MailMessage)
            ->subject('Booking Cancellation - ' . $court->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your badminton court booking has been cancelled.')
            ->line('**Cancelled Booking Details:**')
            ->line('🏸 **Court:** ' . $court->name)
            ->line('📅 **Date:** ' . $date)
            ->line('⏰ **Time:** ' . $startTime . ' - ' . $endTime)
            ->line('📋 **Booking ID:** #' . $this->booking->id);

        if ($this->reason) {
            $mailMessage->line('**Reason for Cancellation:** ' . $this->reason);
        }

        $mailMessage->line('**Refund Information:**')
            ->line('If you have already paid for this booking, a refund will be processed within 3-5 business days.')
            ->line('The refund will be credited back to your original payment method.')
            ->action('Book Another Court', url('/courts'))
            ->line('We apologize for any inconvenience caused.')
            ->line('If you have any questions about the cancellation or refund, please contact our support team.')
            ->salutation('Best regards, The SmashZone Team');

        return $mailMessage;
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
            'cancellation_reason' => $this->reason,
        ];
    }
}
