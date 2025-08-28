<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Booking;
use App\Services\RefundService;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isOwner()) {
            // For owners, show all refunds - they should see everything related to their business
            $refunds = Refund::with(['user', 'booking.court', 'payment.booking.court'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif ($user->isStaff()) {
            // Staff can view all refunds
            $refunds = Refund::with(['user', 'booking.court', 'payment'])->orderBy('created_at', 'desc')->paginate(15);
        } else {
            // Customers can only view their own refunds
            $refunds = Refund::where('user_id', $user->id)
                ->with(['user', 'booking.court', 'payment'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }
        
        return view('refunds.index', compact('refunds'));
    }

    public function show(Refund $refund)
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->isCustomer() && $refund->user_id !== $user->id) {
            abort(403, 'Unauthorized access to refund.');
        }
        
        if ($user->isOwner() && $refund->booking && $refund->booking->court->owner_id !== $user->id) {
            abort(403, 'Unauthorized access to refund.');
        }
        
        return view('refunds.show', compact('refund'));
    }

    public function retry(Refund $refund)
    {
        $user = auth()->user();
        
        // Only owners and staff can retry failed refunds
        if (!$user->isOwner() && !$user->isStaff()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($refund->status !== 'failed') {
            return redirect()->back()->with('error', 'Only failed refunds can be retried.');
        }
        
        $refundService = new RefundService();
        
        try {
            if (!$refund->booking) {
                return redirect()->back()->with('error', 'Cannot retry refund: original booking no longer exists.');
            }
            $refundService->processRefund($refund->booking, $refund->reason);
            return redirect()->back()->with('success', 'Refund retry initiated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to retry refund: ' . $e->getMessage());
        }
    }

    public function manualRefund(Request $request, Refund $refund)
    {
        $user = auth()->user();
        
        // Only owners and staff can process manual refunds
        if (!$user->isOwner() && !$user->isStaff()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($refund->status !== 'pending' && $refund->status !== 'failed') {
            return redirect()->back()->with('error', 'Refund cannot be manually processed.');
        }
        
        $request->validate([
            'manual_refund_notes' => 'required|string|max:500'
        ]);
        
        $refund->update([
            'status' => 'completed',
            'refunded_at' => now(),
            'reason' => $refund->reason . ' (Manual refund processed by ' . $user->name . ': ' . $request->manual_refund_notes . ')'
        ]);
        
        // Send notification
        try {
            $refund->user->notify(new \App\Notifications\RefundProcessed($refund));
        } catch (\Exception $e) {
            \Log::error('Failed to send refund notification', [
                'refund_id' => $refund->id,
                'error' => $e->getMessage()
            ]);
        }
        
        return redirect()->back()->with('success', 'Manual refund processed successfully.');
    }
}
