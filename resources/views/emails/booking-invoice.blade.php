<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - Booking #{{ $booking->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #3b82f6;
            margin: 0;
            font-size: 24px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-box {
            flex: 1;
        }
        .info-box h3 {
            margin-top: 0;
            color: #666;
            font-size: 14px;
        }
        .info-box p {
            margin: 5px 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th,
        .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .details-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .total-label {
            width: 150px;
            font-weight: bold;
        }
        .total-amount {
            width: 120px;
            text-align: right;
            font-size: 18px;
            color: #3b82f6;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SmashZone - Booking Invoice</h1>
    </div>

    <div class="invoice-info">
        <div class="info-box">
            <h3>Booking Information</h3>
            <p><strong>Invoice Number:</strong> #{{ $booking->id }}</p>
            <p><strong>Booking Date:</strong> {{ \Carbon\Carbon::parse($booking->date)->format('F j, Y') }}</p>
            <p><strong>Court:</strong> {{ $booking->court->name ?? 'N/A' }}</p>
            <p><strong>Time:</strong> {{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)->format('g:i A') }}</p>
        </div>
        <div class="info-box">
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> {{ $booking->user->name }}</p>
            <p><strong>Email:</strong> {{ $booking->user->email }}</p>
            @if($booking->user->phone)
            <p><strong>Phone:</strong> {{ $booking->user->phone }}</p>
            @endif
        </div>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Date</th>
                <th>Time</th>
                <th>Duration</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Court Booking - {{ $booking->court->name ?? 'Court' }}</td>
                <td>{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</td>
                <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)->format('g:i A') }}</td>
                <td>
                    @php
                        $start = \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time);
                        $end = \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time);
                        $duration = $start->diffInHours($end);
                    @endphp
                    {{ $duration }} hour(s)
                </td>
                <td style="text-align: right;">RM {{ number_format($booking->total_price, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <div class="total-label">Subtotal:</div>
            <div class="total-amount">RM {{ number_format($booking->total_price, 2) }}</div>
        </div>
        <div class="total-row">
            <div class="total-label">Total Amount:</div>
            <div class="total-amount">RM {{ number_format($booking->total_price, 2) }}</div>
        </div>
    </div>

    @if($payment)
    <div style="margin-top: 30px; padding: 15px; background-color: #f0f9ff; border-left: 4px solid #3b82f6;">
        <p style="margin: 0;"><strong>Payment Status:</strong> Paid</p>
        <p style="margin: 5px 0 0 0;"><strong>Payment Date:</strong> {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y g:i A') : 'N/A' }}</p>
        @if($payment->stripe_payment_intent_id)
        <p style="margin: 5px 0 0 0;"><strong>Transaction ID:</strong> {{ $payment->stripe_payment_intent_id }}</p>
        @endif
    </div>
    @endif

    <div class="footer">
        <p>Thank you for choosing SmashZone!</p>
        <p>This is an automated invoice. For any inquiries, please contact us.</p>
        <p>Generated on {{ now()->format('F j, Y g:i A') }}</p>
    </div>
</body>
</html>

