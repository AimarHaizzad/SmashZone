<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shipping;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display user's orders
     */
    public function index(Request $request)
    {
        // If owner or staff, show all orders management page
        if (auth()->user()->isOwner() || auth()->user()->isStaff()) {
            return $this->manage($request);
        }

        // Regular users see their own orders
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product', 'shipping'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Manage all orders (for owners/staff)
     */
    public function manage(Request $request)
    {
        if (!auth()->user()->isOwner() && !auth()->user()->isStaff()) {
            abort(403, 'Unauthorized. Only owners and staff can manage orders.');
        }

        $query = Order::with(['items.product', 'shipping', 'user']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by shipping status
        if ($request->has('shipping_status') && $request->shipping_status) {
            $query->whereHas('shipping', function($q) use ($request) {
                $q->where('status', $request->shipping_status);
            });
        }

        // Filter by delivery method
        if ($request->has('delivery_method') && $request->delivery_method) {
            $query->where('delivery_method', $request->delivery_method);
        }

        // Search by order number or customer name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('orders.manage', compact('orders', 'stats'));
    }

    /**
     * Show order details
     */
    public function show(Order $order)
    {
        // Ensure user owns this order or is owner/staff
        if ($order->user_id !== auth()->id() && !auth()->user()->isOwner() && !auth()->user()->isStaff()) {
            abort(403, 'Unauthorized access.');
        }

        $order->load(['items.product', 'shipping', 'user', 'payment']);

        // Use different view for owners/staff
        if (auth()->user()->isOwner() || auth()->user()->isStaff()) {
            return view('orders.manage-show', compact('order'));
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Track order by order number
     */
    public function track(Request $request)
    {
        $orderNumber = $request->input('order_number');
        
        if (!$orderNumber) {
            return view('orders.track')->with('error', 'Please enter an order number.');
        }

        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'shipping', 'user'])
            ->first();

        if (!$order) {
            return view('orders.track')->with('error', 'Order not found. Please check your order number.');
        }

        // If user is logged in and owns the order, redirect to order details
        if (auth()->check() && $order->user_id === auth()->id()) {
            return redirect()->route('orders.show', $order);
        }

        return view('orders.track', compact('order'));
    }

    /**
     * Update shipping status (for owners/staff)
     */
    public function updateShippingStatus(Request $request, Order $order)
    {
        if (!auth()->user()->isOwner() && !auth()->user()->isStaff()) {
            abort(403, 'Unauthorized. Only owners and staff can update shipping status.');
        }

        $request->validate([
            'status' => 'required|in:pending,preparing,ready_for_pickup,picked_up,in_transit,out_for_delivery,delivered,failed,returned',
            'tracking_number' => 'nullable|string|max:255',
            'carrier' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $shipping = $order->shipping;
        if (!$shipping) {
            $shipping = Shipping::create([
                'order_id' => $order->id,
                'status' => $request->status,
                'carrier' => $request->carrier,
            ]);
        }

        $updateData = [
            'status' => $request->status,
            'notes' => $request->notes,
        ];

        if ($request->tracking_number) {
            $updateData['tracking_number'] = $request->tracking_number;
        } else if ($request->status === 'in_transit' && !$shipping->tracking_number) {
            // Auto-generate tracking number when shipping
            $updateData['tracking_number'] = Shipping::generateTrackingNumber($request->carrier);
        }

        if ($request->carrier) {
            $updateData['carrier'] = $request->carrier;
        }

        // Update timestamps based on status
        if ($request->status === 'in_transit' && !$shipping->shipped_at) {
            $updateData['shipped_at'] = now();
        }

        if ($request->status === 'delivered' && !$shipping->delivered_at) {
            $updateData['delivered_at'] = now();
            // Also update order status
            $order->update(['status' => 'delivered']);
        }

        $shipping->update($updateData);

        return redirect()->back()->with('success', 'Shipping status updated successfully.');
    }

    /**
     * Update order status (for owners/staff)
     */
    public function updateStatus(Request $request, Order $order)
    {
        if (!auth()->user()->isOwner() && !auth()->user()->isStaff()) {
            abort(403, 'Unauthorized. Only owners and staff can update order status.');
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,return_requested',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Mark order as received (for customers)
     */
    public function markAsReceived(Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow if order is delivered
        if ($order->status !== 'delivered') {
            return redirect()->back()->with('error', 'Order must be delivered before marking as received.');
        }

        // Check if already received
        if ($order->received_at) {
            return redirect()->back()->with('info', 'Order has already been marked as received.');
        }

        $order->update([
            'received_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Order marked as received. Thank you!');
    }

    /**
     * Request order return (for customers)
     */
    public function requestReturn(Request $request, Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow if order is delivered
        if ($order->status !== 'delivered') {
            return redirect()->back()->with('error', 'Order must be delivered before requesting a return.');
        }

        // Check if return already requested
        if ($order->return_requested_at) {
            return redirect()->back()->with('info', 'Return has already been requested for this order.');
        }

        $request->validate([
            'return_reason' => 'required|string|max:1000',
        ]);

        $order->update([
            'return_requested_at' => now(),
            'return_reason' => $request->return_reason,
            'status' => 'return_requested',
        ]);

        // Update shipping status if exists
        if ($order->shipping) {
            $order->shipping->update(['status' => 'returned']);
        }

        return redirect()->back()->with('success', 'Return request submitted successfully. We will process your request shortly.');
    }
}

