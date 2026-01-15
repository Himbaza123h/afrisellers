<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF; // You may need to install barryvdh/laravel-dompdf

class OrderController extends Controller
{
/**
 * Display a listing of vendor's orders.
 */
public function index(Request $request)
{
    $query = Order::with(['buyer', 'items.product', 'shippingAddress'])
        ->where('vendor_id', auth()->id());

    // Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('order_number', 'like', "%{$search}%")
              ->orWhereHas('buyer', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    // Status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Payment status filter
    if ($request->filled('payment_status')) {
        $query->where('payment_status', $request->payment_status);
    }

    // Date range filter (using flatpickr format)
    if ($request->filled('date_range')) {
        $dates = explode(' to ', $request->date_range);
        if (count($dates) === 2) {
            $query->whereDate('created_at', '>=', $dates[0])
                  ->whereDate('created_at', '<=', $dates[1]);
        }
    }

    // Sorting
    $sortBy = $request->get('sort_by', 'created_at');
    $sortOrder = $request->get('sort_order', 'desc');
    $query->orderBy($sortBy, $sortOrder);

    $orders = $query->paginate(15)->withQueryString();

    // Statistics
    $vendorId = auth()->id();
    $stats = [
        'total' => Order::where('vendor_id', $vendorId)->count(),
        'pending' => Order::where('vendor_id', $vendorId)->where('status', 'pending')->count(),
        'processing' => Order::where('vendor_id', $vendorId)->where('status', 'processing')->count(),
        'completed' => Order::where('vendor_id', $vendorId)->where('status', 'delivered')->count(),
        'total_revenue' => Order::where('vendor_id', $vendorId)
            ->whereIn('status', ['delivered', 'shipped'])
            ->sum('total'),
        'cancelled' => Order::where('vendor_id', $vendorId)->where('status', 'cancelled')->count(),
        'avg_order_value' => Order::where('vendor_id', $vendorId)
            ->whereIn('status', ['delivered', 'shipped'])
            ->avg('total'),
    ];

    // Calculate percentages
    $stats['pending_percentage'] = $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100) : 0;
    $stats['processing_percentage'] = $stats['total'] > 0 ? round(($stats['processing'] / $stats['total']) * 100) : 0;
    $stats['completed_percentage'] = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0;

    return view('orders.index', compact('orders', 'stats'));
}

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Ensure vendor can only view their own orders
        if ($order->vendor_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['buyer', 'items.product', 'shippingAddress', 'billingAddress']);

        return view('orders.show', compact('order'));
    }



    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $vendorId = Auth::id();

        // Get all buyers (users with buyer role)
        $buyers = User::whereHas('roles', function($query) {
            $query->where('slug', 'buyer');
        })->get();

        // Get vendor's products
        $products = Product::where('user_id', $vendorId)
            ->where('status', 'active')
            ->get();

        return view('vendor.orders.create', compact('buyers', 'products'));
    }


    public function store(Request $request)
{
    $request->validate([
        'buyer_id' => 'required|exists:users,id',
        'products' => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
        'products.*.price' => 'required|numeric|min:0',
        'shipping_fee' => 'nullable|numeric|min:0',
        'tax' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string|max:1000',
        'shipping_address_id' => 'nullable|exists:addresses,id',
        'status' => 'required|in:pending,confirmed,processing',
        'payment_status' => 'required|in:pending,paid',
        'payment_method' => 'nullable|in:cash,credit_card,bank_transfer,paypal,stripe,other',
        'payment_reference' => 'nullable|string|max:255',
    ]);

    try {
        DB::beginTransaction();

        // Calculate totals
        $subtotal = 0;
        foreach ($request->products as $item) {
            $subtotal += $item['quantity'] * $item['price'];
        }

        $tax = $request->tax ?? 0;
        $shippingFee = $request->shipping_fee ?? 0;
        $total = $subtotal + $tax + $shippingFee;

        // Create order
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'buyer_id' => $request->buyer_id,
            'vendor_id' => Auth::id(),
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping_fee' => $shippingFee,
            'total' => $total,
            'currency' => 'USD',
            'notes' => $request->notes,
            'shipping_address_id' => $request->shipping_address_id,
            'confirmed_at' => $request->status === 'confirmed' ? now() : null,
        ]);

        // Create order items
        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal' => $item['quantity'] * $item['price'],
                'tax' => 0,
            ]);

            // Update product stock if needed
            if (isset($product->track_inventory) && $product->track_inventory) {
                $product->decrement('stock_quantity', $item['quantity']);
            }
        }

        // Create transaction
        $transaction = Transaction::create([
            'transaction_number' => Transaction::generateTransactionNumber(),
            'order_id' => $order->id,
            'buyer_id' => $request->buyer_id,
            'vendor_id' => Auth::id(),
            'type' => 'order',
            'status' => $request->payment_status === 'paid' ? 'completed' : 'pending',
            'amount' => $total,
            'currency' => 'USD',
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'notes' => 'Payment for Order #' . $order->order_number,
            'completed_at' => $request->payment_status === 'paid' ? now() : null,
        ]);

        DB::commit();

        return redirect()->route('vendor.orders.index')
            ->with('success', 'Order created successfully! Order Number: ' . $order->order_number . ' | Transaction: ' . $transaction->transaction_number);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()
            ->withErrors(['error' => 'Failed to create order: ' . $e->getMessage()]);
    }
}



    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Ensure vendor can only update their own orders
        if ($order->vendor_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        // Update timestamp based on status
        switch ($request->status) {
            case 'confirmed':
                $order->update(['confirmed_at' => now()]);
                break;
            case 'shipped':
                $order->update(['shipped_at' => now()]);
                break;
            case 'delivered':
                $order->update(['delivered_at' => now()]);
                break;
            case 'cancelled':
                $order->update(['cancelled_at' => now()]);
                break;
        }

        return back()->with('success', 'Order status updated successfully!');
    }

    /**
     * Get buyer's addresses
     */
    public function getBuyerAddresses($buyerId)
    {
        $addresses = Address::where('user_id', $buyerId)->get();
        return response()->json($addresses);
    }

    /**
     * Get product details
     */
    public function getProductDetails($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock_quantity' => $product->stock_quantity,
            'track_inventory' => $product->track_inventory,
        ]);
    }

    /**
 * Get product price based on quantity
 */
public function getProductPrice($productId, $quantity)
{
    $product = Product::with('prices')->find($productId);

    if (!$product) {
        return response()->json(['error' => 'Product not found'], 404);
    }

    // Find the applicable price based on quantity
    $price = $product->prices()
        ->where('min_qty', '<=', $quantity)
        ->where(function($query) use ($quantity) {
            $query->where('max_qty', '>=', $quantity)
                  ->orWhereNull('max_qty');
        })
        ->orderBy('min_qty', 'desc')
        ->first();

    return response()->json([
        'id' => $product->id,
        'name' => $product->name,
        'price' => $price ? $price->price : 0,
        'currency' => $price ? $price->currency : 'USD',
        'has_price' => $price !== null,
    ]);
}


    /**
     * Accept a pending order.
     */
    public function accept(Order $order)
    {
        if ($order->vendor_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        if ($order->status != 'pending') {
            return back()->withErrors(['error' => 'Only pending orders can be accepted.']);
        }

        try {
            $order->confirm();
            return back()->with('success', 'Order accepted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to accept order: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark order as processing.
     */
    public function process(Order $order)
    {
        if ($order->vendor_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->withErrors(['error' => 'Order cannot be processed in current status.']);
        }

        try {
            $order->markAsProcessing();
            return back()->with('success', 'Order marked as processing.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to process order: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark order as shipped.
     */
    public function ship(Request $request, Order $order)
    {
        if ($order->vendor_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        if (!in_array($order->status, ['confirmed', 'processing'])) {
            return back()->withErrors(['error' => 'Order cannot be shipped in current status.']);
        }

        try {
            $order->ship();
            return back()->with('success', 'Order marked as shipped.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to ship order: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark order as completed/delivered.
     */
    public function complete(Order $order)
    {
        if ($order->vendor_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        if ($order->status != 'shipped') {
            return back()->withErrors(['error' => 'Only shipped orders can be marked as delivered.']);
        }

        try {
            $order->deliver();
            return back()->with('success', 'Order marked as delivered.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to complete order: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel an order.
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->vendor_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        if (!$order->is_cancellable) {
            return back()->withErrors(['error' => 'This order cannot be cancelled.']);
        }

        try {
            DB::beginTransaction();

            $order->cancel();

            // Restore inventory if needed
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            DB::commit();

            return back()->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to cancel order: ' . $e->getMessage()]);
        }
    }

    /**
     * Process refund for an order.
     */
    public function refund(Request $request, Order $order)
    {
        if ($order->vendor_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . $order->total,
            'refund_reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Here you would integrate with your payment gateway to process the refund
            // For now, we'll just update the order status

            $order->update([
                'status' => 'refunded',
                'refund_amount' => $request->refund_amount,
                'refund_reason' => $request->refund_reason,
                'refunded_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Refund processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process refund: ' . $e->getMessage()]);
        }
    }

    /**
     * Display order invoice.
     */
    public function invoice(Order $order)
    {
        if ($order->vendor_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['buyer', 'items.product', 'shippingAddress', 'billingAddress']);

        return view('orders.invoice', compact('order'));
    }

    /**
     * Download order invoice as PDF.
     */
    public function downloadInvoice(Order $order)
    {
        if ($order->vendor_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['buyer', 'items.product', 'shippingAddress', 'billingAddress']);

        $pdf = PDF::loadView('orders.invoice-pdf', compact('order'));

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}
