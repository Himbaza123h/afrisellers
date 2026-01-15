<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\RFQs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the buyer dashboard with real data.
     */
    public function index()
    {
        $user = Auth::user();
        // dd($user->id);

        // Get order statistics
        $stats = [
            'total_orders' => Order::byBuyer($user->id)->count(),
            'pending_orders' => Order::byBuyer($user->id)->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped'])->count(),
            'active_rfqs' => RFQs::where('user_id', $user->id)->where('status', 'active')->count(),
        ];

        // Get recent orders with items and product images
        $recentOrders = Order::byBuyer($user->id)
            ->with(['items.product.images', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get active RFQs
        $activeRfqs = RFQs::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        // Get recommended products (based on categories from user's orders or popular products)
        $recommendedProducts = $this->getRecommendedProducts($user->id);

        return view('buyer.dashboard.index', compact(
            'stats',
            'recentOrders',
            'activeRfqs',
            'recommendedProducts'
        ));
    }

    /**
     * Get recommended products for the user
     */
    private function getRecommendedProducts($userId)
    {
        // Get categories from user's previous orders
        $orderedCategoryIds = Order::byBuyer($userId)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.deleted_at', null)
            ->pluck('products.product_category_id')
            ->unique()
            ->toArray();

        // Get recommended products from those categories
        $recommended = Product::with(['images',  'productCategory'])
            ->where('status', 'active')
            ->where('is_admin_verified', true)
            ->when(!empty($orderedCategoryIds), function ($query) use ($orderedCategoryIds) {
                return $query->whereIn('product_category_id', $orderedCategoryIds);
            })
            ->inRandomOrder()
            ->limit(8)
            ->get();

        // If not enough products from user's categories, add popular products
        if ($recommended->count() < 8) {
            $additionalProducts = Product::with(['images',  'productCategory'])
                ->where('status', 'active')
                ->where('is_admin_verified', true)
                ->whereNotIn('id', $recommended->pluck('id'))
                ->inRandomOrder()
                ->limit(8 - $recommended->count())
                ->get();

            $recommended = $recommended->merge($additionalProducts);
        }

        return $recommended;
    }

    /**
     * Show all orders for the buyer
     */
    public function orders(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Order::byBuyer(Auth::id())
            ->with(['items.product.images', 'vendor', 'shippingAddress'])
            ->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10);
        $statuses = Order::statuses();

        return view('buyer.orders.index', compact('orders', 'statuses', 'status'));
    }

    /**
     * Show order details
     */
    public function showOrder($id)
    {
        $order = Order::byBuyer(Auth::id())
            ->with(['items.product.images', 'vendor', 'shippingAddress', 'billingAddress'])
            ->findOrFail($id);

        return view('buyer.orders.show', compact('order'));
    }

    /**
     * Cancel an order
     */
    public function cancelOrder($id)
    {
        $order = Order::byBuyer(Auth::id())->findOrFail($id);

        if (!$order->is_cancellable) {
            return back()->with('error', __('messages.order_not_cancellable'));
        }

        $order->cancel();

        return back()->with('success', __('messages.order_cancelled_successfully'));
    }
}
