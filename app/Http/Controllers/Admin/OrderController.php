<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['buyer', 'vendor', 'items.product', 'shippingAddress']);

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('buyer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('vendor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Payment status filter
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Vendor filter
        if ($request->has('vendor') && $request->vendor != '') {
            $query->where('vendor_id', $request->vendor);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(15);

        // Get vendors for filter
        $vendors = User::whereHas('vendor')->get();

        // Statistics
        $stats = $this->getOrderStats();

        return view('orders.index', compact('orders', 'vendors', 'stats'));
    }

    /**
     * Print orders report
     */
    public function print()
    {
        $user = Auth::user();
        // check if user is admin

        if($user->vendor){
        $orders = Order::with(['buyer', 'vendor', 'items.product'])
        ->where('vendor_id', $user->id)
        ->orderBy('created_at', 'desc')->get();
        } else {
         $orders = Order::with(['buyer', 'vendor', 'items.product'])
        ->orderBy('created_at', 'desc')->get();
        }

        $stats = $this->getOrderStats();

        return view('orders.print', compact('orders', 'stats'));
    }

    /**
     * Get order statistics
     */
    private function getOrderStats()
    {
        $total = Order::count();
        $pending = Order::where('status', 'pending')->count();
        $processing = Order::where('status', 'processing')->count();
        $confirmed = Order::where('status', 'confirmed')->count();
        $shipped = Order::where('status', 'shipped')->count();
        $delivered = Order::where('status', 'delivered')->count();
        $cancelled = Order::where('status', 'cancelled')->count();

        $paid = Order::where('payment_status', 'paid')->count();
        $pendingPayment = Order::where('payment_status', 'pending')->count();

        $totalRevenue = Order::whereIn('status', ['delivered', 'shipped'])->sum('total');
        $averageOrderValue = $delivered > 0 ? $totalRevenue / $delivered : 0;
        $todayOrders = Order::whereDate('created_at', today())->count();
        $thisWeekOrders = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'processing' => $processing,
            'confirmed' => $confirmed,
            'shipped' => $shipped,
            'delivered' => $delivered,
            'cancelled' => $cancelled,
            'paid' => $paid,
            'pending_payment' => $pendingPayment,
            'total_revenue' => $totalRevenue,
            'avg_order_value' => $averageOrderValue,
            'today' => $todayOrders,
            'this_week' => $thisWeekOrders,

            // Percentages
            'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 1) : 0,
            'processing_percentage' => $total > 0 ? round(($processing / $total) * 100, 1) : 0,
            'delivered_percentage' => $total > 0 ? round(($delivered / $total) * 100, 1) : 0,
            'paid_percentage' => $total > 0 ? round(($paid / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $buyers = User::whereHas('buyer')->get();
        $vendors = User::whereHas('vendor')->get();
        $products = Product::where('status', 'active')->get();

        return view('orders.create', compact('buyers', 'vendors', 'products'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'buyer_id' => 'required|exists:users,id',
            'vendor_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'shipping_fee' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'buyer_id' => $validated['buyer_id'],
                'vendor_id' => $validated['vendor_id'],
                'status' => 'pending',
                'shipping_fee' => $validated['shipping_fee'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'currency' => $validated['currency'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);

                // Decrease product stock
                $product->decrement('stock', $item['quantity']);
            }

            // Calculate totals
            $order->calculateTotals();

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create order: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['buyer', 'vendor', 'items.product', 'shippingAddress', 'billingAddress']);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $order->load(['items.product']);
        $buyers = User::whereHas('buyer')->get();
        $vendors = User::whereHas('vendor')->get();
        $products = Product::where('status', 'active')->get();

        return view('orders.edit', compact('order', 'buyers', 'vendors', 'products'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'shipping_fee' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $order->update([
                'status' => $validated['status'],
                'shipping_fee' => $validated['shipping_fee'] ?? $order->shipping_fee,
                'tax' => $validated['tax'] ?? $order->tax,
                'notes' => $validated['notes'] ?? $order->notes,
            ]);

            // Update status timestamps
            if ($validated['status'] == 'confirmed' && !$order->confirmed_at) {
                $order->update(['confirmed_at' => now()]);
            } elseif ($validated['status'] == 'shipped' && !$order->shipped_at) {
                $order->update(['shipped_at' => now()]);
            } elseif ($validated['status'] == 'delivered' && !$order->delivered_at) {
                $order->update(['delivered_at' => now()]);
            } elseif ($validated['status'] == 'cancelled' && !$order->cancelled_at) {
                $order->update(['cancelled_at' => now()]);
            }

            // Recalculate totals
            $order->calculateTotals();

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update order: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();

            // Restore inventory
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            $order->delete();

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Order deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete order: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel an order.
     */
    public function cancel(Request $request, Order $order)
    {
        if (!$order->is_cancellable) {
            return back()->withErrors(['error' => 'This order cannot be cancelled.']);
        }

        try {
            DB::beginTransaction();

            $order->cancel();

            // Restore inventory
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
     * Display order invoice.
     */
    public function invoice(Order $order)
    {
        $order->load(['buyer', 'vendor', 'items.product', 'shippingAddress', 'billingAddress']);

        return view('orders.invoice', compact('order'));
    }

    /**
     * Download order invoice as PDF.
     */
    public function downloadInvoice(Order $order)
    {
        $order->load(['buyer', 'vendor', 'items.product', 'shippingAddress', 'billingAddress']);

        $pdf = PDF::loadView('admin.order.invoice-pdf', compact('order'));

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}
