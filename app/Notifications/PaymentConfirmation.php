<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payment;

class PaymentConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
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
        $booking = $this->payment->booking;
        $court = $booking->court;
        $startTime = \Carbon\Carbon::parse($booking->start_time)->format('g:i A');
        $endTime = \Carbon\Carbon::parse($booking->end_time)->format('g:i A');
        $date = \Carbon\Carbon::parse($booking->date)->format('l, F j, Y');

        return (new MailMessage)
            ->subject('Payment Confirmation - SmashZone')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your payment has been successfully processed!')
            ->line('**Payment Details:**')
            ->line('💳 **Payment ID:** #' . $this->payment->id)
            ->line('💰 **Amount Paid:** RM ' . number_format($this->payment->amount, 2))
            ->line('📅 **Payment Date:** ' . $this->payment->payment_date->format('l, F j, Y g:i A'))
            ->line('🏸 **Court:** ' . $court->name)
            ->line('📅 **Booking Date:** ' . $date)
            ->line('⏰ **Time:** ' . $startTime . ' - ' . $endTime)
            ->line('📋 **Booking ID:** #' . $booking->id)
            ->action('View Booking Details', url('/bookings/' . $booking->id))
            ->line('Your booking is now confirmed and ready!')
            ->line('Please arrive 10 minutes before your scheduled time.')
            ->line('Thank you for choosing SmashZone!')
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
            'payment_id' => $this->payment->id,
            'booking_id' => $this->payment->booking_id,
            'amount' => $this->payment->amount,
            'payment_date' => $this->payment->payment_date,
            'court_name' => $this->payment->booking->court->name,
        ];
    }
}
