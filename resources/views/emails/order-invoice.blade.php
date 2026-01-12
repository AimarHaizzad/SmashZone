<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - Order {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #10b981;
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
        .details-table td:last-child,
        .details-table th:last-child {
            text-align: right;
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
            color: #10b981;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .delivery-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SmashZone - Order Invoice</h1>
    </div>

    <div class="invoice-info">
        <div class="info-box">
            <h3>Order Information</h3>
            <p><strong>Invoice Number:</strong> {{ $order->order_number }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y') }}</p>
            <p><strong>Order Status:</strong> {{ ucfirst($order->status) }}</p>
            <p><strong>Delivery Method:</strong> {{ $order->delivery_method_label }}</p>
        </div>
        <div class="info-box">
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> {{ $order->user->name }}</p>
            <p><strong>Email:</strong> {{ $order->user->email }}</p>
            @if($order->user->phone)
            <p><strong>Phone:</strong> {{ $order->user->phone }}</p>
            @endif
        </div>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>RM {{ number_format($item->product_price, 2) }}</td>
                <td>RM {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <div class="total-label">Subtotal:</div>
            <div class="total-amount">RM {{ number_format($order->total_amount, 2) }}</div>
        </div>
        <div class="total-row">
            <div class="total-label">Total Amount:</div>
            <div class="total-amount">RM {{ number_format($order->total_amount, 2) }}</div>
        </div>
    </div>

    @if($order->delivery_method === 'delivery' && $order->delivery_address)
    <div class="delivery-info">
        <h3 style="margin-top: 0;">Delivery Address</h3>
        <p style="margin: 5px 0;"><strong>Address:</strong> {{ $order->delivery_address }}</p>
        <p style="margin: 5px 0;"><strong>City:</strong> {{ $order->delivery_city }}</p>
        <p style="margin: 5px 0;"><strong>Postcode:</strong> {{ $order->delivery_postcode }}</p>
        <p style="margin: 5px 0;"><strong>State:</strong> {{ $order->delivery_state }}</p>
        @if($order->delivery_phone)
        <p style="margin: 5px 0;"><strong>Phone:</strong> {{ $order->delivery_phone }}</p>
        @endif
    </div>
    @else
    <div class="delivery-info">
        <p style="margin: 0;"><strong>Pickup Method:</strong> Self Pickup</p>
        <p style="margin: 5px 0 0 0;">Please collect your order from our store.</p>
    </div>
    @endif

    @if($payment)
    <div style="margin-top: 30px; padding: 15px; background-color: #f0f9ff; border-left: 4px solid #3b82f6;">
        <p style="margin: 0;"><strong>Payment Status:</strong> Paid</p>
        <p style="margin: 5px 0 0 0;"><strong>Payment Date:</strong> {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y g:i A') : 'N/A' }}</p>
        @if($payment->stripe_payment_intent_id)
        <p style="margin: 5px 0 0 0;"><strong>Transaction ID:</strong> {{ $payment->stripe_payment_intent_id }}</p>
        @endif
    </div>
    @endif

    @if($order->notes)
    <div style="margin-top: 20px; padding: 15px; background-color: #fffbeb; border-left: 4px solid #f59e0b;">
        <p style="margin: 0;"><strong>Order Notes:</strong></p>
        <p style="margin: 5px 0 0 0;">{{ $order->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for choosing SmashZone!</p>
        <p>This is an automated invoice. For any inquiries, please contact us.</p>
        <p>Generated on {{ now()->format('F j, Y g:i A') }}</p>
    </div>
</body>
</html>
