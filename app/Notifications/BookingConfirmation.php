<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;
    public $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, $payment = null)
    {
        $this->booking = $booking;
        $this->payment = $payment ?? $booking->payment;
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
            ->subject('Booking Confirmation - ' . $court->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your badminton court booking has been confirmed!')
            ->line('**Booking Details:**')
            ->line('🏸 **Court:** ' . $court->name)
            ->line('📅 **Date:** ' . $date)
            ->line('⏰ **Time:** ' . $startTime . ' - ' . $endTime)
            ->line('💰 **Total Amount:** RM ' . number_format($this->booking->total_price, 2))
            ->line('📋 **Booking ID:** #' . $this->booking->id);

        // Add invoice PDF attachment if payment exists
        if ($this->payment && $this->payment->status === 'paid') {
            try {
                $pdf = Pdf::loadView('emails.booking-invoice', [
                    'booking' => $this->booking,
                    'payment' => $this->payment
                ]);
                
                $invoiceFileName = 'booking-invoice-' . $this->booking->id . '.pdf';
                $mailMessage->attachData($pdf->output(), $invoiceFileName, [
                    'mime' => 'application/pdf',
                ]);
                
                $mailMessage->line('📄 **Invoice attached** - Please find your invoice PDF attached to this email.');
            } catch (\Exception $e) {
                \Log::error('Failed to attach invoice PDF', [
                    'booking_id' => $this->booking->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $mailMessage
            ->action('View Booking Details', url('/bookings/' . $this->booking->id))
            ->line('Please arrive 10 minutes before your scheduled time.')
            ->line('If you need to cancel or modify your booking, please contact us at least 24 hours in advance.')
            ->line('Thank you for choosing SmashZone!')
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
            'total_price' => $this->booking->total_price,
        ];
    }
}
