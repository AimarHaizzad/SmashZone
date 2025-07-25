<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['user', 'booking.court'])->orderBy('payment_date', 'desc')->get();
        return view('payments.index', compact('payments'));
    }
}
