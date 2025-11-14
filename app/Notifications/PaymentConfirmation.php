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
        $bookings = $this->payment->bookings()->with('court')->get();

        return (new MailMessage)
            ->subject('Payment Confirmation - SmashZone')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your payment has been successfully processed!')
            ->line('**Payment Details:**')
            ->line('💳 **Payment ID:** #' . $this->payment->id)
            ->line('💰 **Amount Paid:** RM ' . number_format($this->payment->amount, 2))
            ->line('📅 **Payment Date:** ' . $this->payment->payment_date->format('l, F j, Y g:i A'))
            ->line('🏸 **Bookings:**')
            ->line($bookings->isNotEmpty()
                ? $bookings->map(function ($booking) {
                    $date = \Carbon\Carbon::parse($booking->date)->format('M d, Y');
                    $start = \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->format('g:i A');
                    $end = \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)->format('g:i A');
                    return '- ' . ($booking->court->name ?? 'Court ' . $booking->court_id) . ' on ' . $date . ' (' . $start . ' - ' . $end . ')';
                })->implode("\n")
                : '- No booking details available'
            )
            ->action('View My Bookings', route('bookings.my'))
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
            'booking_ids' => $this->payment->bookings()->pluck('id'),
            'amount' => $this->payment->amount,
            'payment_date' => $this->payment->payment_date,
            'booking_count' => $this->payment->bookings()->count(),
        ];
    }
}
