<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Refund;
use Stripe\Stripe;
use Stripe\Refund as StripeRefund;
use Illuminate\Support\Facades\Log;
use App\Notifications\RefundProcessed;

class RefundService
{
    /**
     * Process a refund for a booking cancellation
     */
    public function processRefund(Booking $booking, $reason = null)
    {
        // Check if there's a paid payment for this booking
        $payment = $booking->payment;
        
        if (!$payment || $payment->status !== 'paid') {
            Log::info('No paid payment found for booking', [
                'booking_id' => $booking->id,
                'payment_status' => $payment ? $payment->status : 'no_payment'
            ]);
            return null;
        }

        // Check if refund already exists
        $existingRefund = $payment->refunds()->where('status', '!=', 'failed')->first();
        if ($existingRefund) {
            Log::info('Refund already exists for payment', [
                'payment_id' => $payment->id,
                'refund_id' => $existingRefund->id
            ]);
            return $existingRefund;
        }

        // Create refund record
        $refund = Refund::create([
            'payment_id' => $payment->id,
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'amount' => $payment->amount,
            'status' => 'pending',
            'reason' => $reason ?? 'Booking cancelled by customer',
        ]);

        // Process the refund
        try {
            $this->processStripeRefund($refund);
        } catch (\Exception $e) {
            Log::error('Failed to process refund', [
                'refund_id' => $refund->id,
                'error' => $e->getMessage()
            ]);
            
            $refund->update(['status' => 'failed']);
            throw $e;
        }

        return $refund;
    }

    /**
     * Process refund through Stripe
     */
    private function processStripeRefund(Refund $refund)
    {
        $payment = $refund->payment;
        
        // Check if payment was made through Stripe
        if (!$payment->stripe_payment_intent_id) {
            Log::info('Payment was not made through Stripe, marking as manual refund', [
                'refund_id' => $refund->id
            ]);
            
            $refund->update([
                'status' => 'completed',
                'refunded_at' => now()
            ]);
            
            // Send notification
            $this->sendRefundNotification($refund);
            return;
        }

        try {
            // Set Stripe API key
            Stripe::setApiKey(config('services.stripe.secret'));

            // Create Stripe refund
            $stripeRefund = StripeRefund::create([
                'payment_intent' => $payment->stripe_payment_intent_id,
                'amount' => (int)($refund->amount * 100), // Convert to cents
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'refund_id' => $refund->id,
                    'booking_id' => $refund->booking_id,
                    'user_id' => $refund->user_id,
                ]
            ]);

            // Update refund with Stripe refund ID
            $refund->update([
                'stripe_refund_id' => $stripeRefund->id,
                'status' => 'completed',
                'refunded_at' => now()
            ]);

            Log::info('Stripe refund processed successfully', [
                'refund_id' => $refund->id,
                'stripe_refund_id' => $stripeRefund->id
            ]);

            // Send notification
            $this->sendRefundNotification($refund);

        } catch (\Exception $e) {
            Log::error('Stripe refund failed', [
                'refund_id' => $refund->id,
                'error' => $e->getMessage()
            ]);
            
            $refund->update(['status' => 'failed']);
            throw $e;
        }
    }

    /**
     * Send refund notification to user
     */
    private function sendRefundNotification(Refund $refund)
    {
        try {
            $refund->user->notify(new RefundProcessed($refund));
        } catch (\Exception $e) {
            Log::error('Failed to send refund notification', [
                'refund_id' => $refund->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get refund status for a booking
     */
    public function getRefundStatus(Booking $booking)
    {
        $payment = $booking->payment;
        
        if (!$payment) {
            return null;
        }

        return $payment->refunds()
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Check if booking is eligible for refund
     */
    public function isEligibleForRefund(Booking $booking)
    {
        // Check if booking has a paid payment
        $payment = $booking->payment;
        
        if (!$payment || $payment->status !== 'paid') {
            return false;
        }

        // Check if refund already exists and is not failed
        $existingRefund = $payment->refunds()
            ->where('status', '!=', 'failed')
            ->first();

        return !$existingRefund;
    }
}
