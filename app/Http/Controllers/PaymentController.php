<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->isOwner()) {
            $courtIds = $user->courts->pluck('id');
            $payments = \App\Models\Payment::whereHas('booking.court', function($q) use ($courtIds) {
                $q->whereIn('id', $courtIds);
            })->with(['user', 'booking.court'])->orderBy('payment_date', 'desc')->get();
        } elseif ($user->isStaff()) {
            $payments = \App\Models\Payment::with(['user', 'booking.court'])->orderBy('payment_date', 'desc')->get();
        } else {
            $payments = \App\Models\Payment::where('user_id', $user->id)->with(['user', 'booking.court'])->orderBy('payment_date', 'desc')->get();
        }
        return view('payments.index', compact('payments'));
    }
}
