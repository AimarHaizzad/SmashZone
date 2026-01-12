<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
        $order = $this->order->load(['items.product', 'payment', 'shipping']);
        $itemCount = $order->items->sum('quantity');

        $mailMessage = (new MailMessage)
            ->subject('Order Confirmation - ' . $order->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for your purchase! Your order has been confirmed and payment processed successfully.')
            ->line('**Order Details:**')
            ->line('📦 **Order Number:** ' . $order->order_number)
            ->line('📅 **Order Date:** ' . $order->created_at->format('l, F j, Y'))
            ->line('💰 **Total Amount:** RM ' . number_format($order->total_amount, 2))
            ->line('📊 **Items:** ' . $itemCount . ' item(s)')
            ->line('🚚 **Delivery Method:** ' . $order->delivery_method_label);

        // Add product list
        if ($order->items->isNotEmpty()) {
            $mailMessage->line('**Products Purchased:**');
            foreach ($order->items as $item) {
                $mailMessage->line('• ' . $item->product_name . ' (Qty: ' . $item->quantity . ') - RM ' . number_format($item->subtotal, 2));
            }
        }

        // Add delivery information if applicable
        if ($order->delivery_method === 'delivery' && $order->delivery_address) {
            $mailMessage->line('**Delivery Address:**')
                ->line($order->full_delivery_address);
        }

        // Add invoice PDF attachment
        if ($order->payment && $order->payment->status === 'paid') {
            try {
                $pdf = Pdf::loadView('emails.order-invoice', [
                    'order' => $order,
                    'payment' => $order->payment
                ]);
                
                $invoiceFileName = 'order-invoice-' . $order->order_number . '.pdf';
                $mailMessage->attachData($pdf->output(), $invoiceFileName, [
                    'mime' => 'application/pdf',
                ]);
                
                $mailMessage->line('📄 **Invoice attached** - Please find your invoice PDF attached to this email.');
            } catch (\Exception $e) {
                \Log::error('Failed to attach invoice PDF', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $mailMessage
            ->action('View Order Details', url('/orders/' . $order->id))
            ->line('Your order is being processed and will be ' . ($order->delivery_method === 'pickup' ? 'ready for pickup soon' : 'shipped to your address') . '.')
            ->line('You will receive updates about your order status via email.')
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
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total_amount' => $this->order->total_amount,
            'item_count' => $this->order->items()->count(),
            'delivery_method' => $this->order->delivery_method,
        ];
    }
}
