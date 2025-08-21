<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Refund;

class RefundProcessed extends Notification implements ShouldQueue
{
    use Queueable;

    public $refund;

    /**
     * Create a new notification instance.
     */
    public function __construct(Refund $refund)
    {
        $this->refund = $refund;
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
        $booking = $this->refund->booking;
        $court = $booking->court;
        $startTime = \Carbon\Carbon::parse($booking->start_time)->format('g:i A');
        $endTime = \Carbon\Carbon::parse($booking->end_time)->format('g:i A');
        $date = \Carbon\Carbon::parse($booking->date)->format('l, F j, Y');

        $mailMessage = (new MailMessage)
            ->subject('Refund Processed - ' . $court->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your refund has been processed successfully.')
            ->line('**Refund Details:**')
            ->line('💰 **Amount:** ' . $this->refund->formatted_amount)
            ->line('🏸 **Court:** ' . $court->name)
            ->line('📅 **Date:** ' . $date)
            ->line('⏰ **Time:** ' . $startTime . ' - ' . $endTime)
            ->line('📋 **Booking ID:** #' . $booking->id)
            ->line('🆔 **Refund ID:** #' . $this->refund->id);

        if ($this->refund->stripe_refund_id) {
            $mailMessage->line('💳 **Payment Method:** Stripe (Original Payment Method)')
                ->line('The refund will be credited back to your original payment method within 3-5 business days.');
        } else {
            $mailMessage->line('💳 **Payment Method:** Manual Refund')
                ->line('Please contact our support team for manual refund processing.');
        }

        $mailMessage->action('Book Another Court', url('/courts'))
            ->line('Thank you for choosing SmashZone!')
            ->line('If you have any questions about this refund, please contact our support team.')
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
            'refund_id' => $this->refund->id,
            'booking_id' => $this->refund->booking_id,
            'amount' => $this->refund->amount,
            'court_name' => $this->refund->booking->court->name,
            'date' => $this->refund->booking->date,
            'start_time' => $this->refund->booking->start_time,
            'end_time' => $this->refund->booking->end_time,
        ];
    }
}
