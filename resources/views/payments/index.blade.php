@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Payments</h1>
    <div class="bg-white shadow rounded p-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2">User</th>
                    <th class="px-4 py-2">Product</th>
                    <th class="px-4 py-2">Amount</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Payment Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td class="px-4 py-2">{{ $payment->user->name ?? '-' }}</td>
                    <td class="px-4 py-2">
                        @if($payment->booking)
                            Court: {{ $payment->booking->court->name ?? '-' }}<br>
                            Date: {{ $payment->booking->date }}<br>
                            Time: {{ $payment->booking->start_time }} - {{ $payment->booking->end_time }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-2">RM {{ number_format($payment->amount, 2) }}</td>
                    <td class="px-4 py-2">{{ ucfirst($payment->status) }}</td>
                    <td class="px-4 py-2">{{ $payment->payment_date ? $payment->payment_date->format('Y-m-d H:i') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($payments->isEmpty())
            <div class="text-center text-gray-500 py-8">No payments found.</div>
        @endif
    </div>
</div>
@endsection 