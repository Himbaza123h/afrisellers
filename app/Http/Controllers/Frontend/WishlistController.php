<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Analytics\ProductAnalytics;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $wishlists = Wishlist::where('user_id', Auth::id())
                ->with(['product.images', 'product.prices', 'product.country', 'product.productCategory'])
                ->latest()
                ->paginate(20);
        } else {
            $wishlists = Wishlist::where('ip_address', request()->ip())
                ->whereNull('user_id')
                ->with(['product.images', 'product.prices', 'product.country', 'product.productCategory'])
                ->latest()
                ->paginate(20);
        }

        return view('frontend.wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request, Product $product)
    {
        $userId    = Auth::id();
        $ipAddress = $request->ip();

        // Find existing
        $query = Wishlist::where('product_id', $product->id);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id')->where('ip_address', $ipAddress);
        }

        $existing = $query->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
        } else {
            Wishlist::create([
                'product_id' => $product->id,
                'user_id'    => $userId,
                'ip_address' => $userId ? null : $ipAddress,
            ]);
            $wishlisted = true;
        }

        ProductAnalytics::alltime($product->id)->increment('wishlist_adds');

        $count = $this->getCount($userId, $ipAddress);

        return response()->json([
            'wishlisted' => $wishlisted,
            'count'      => $count,
        ]);
    }

    public function remove(Request $request, $id)
    {
        $userId    = Auth::id();
        $ipAddress = $request->ip();

        $query = Wishlist::where('id', $id);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('ip_address', $ipAddress);
        }

        $query->delete();

        return redirect()->back()->with('success', 'Removed from wishlist.');
    }

    public function count(Request $request)
    {
        $count = $this->getCount(Auth::id(), $request->ip());
        return response()->json(['count' => $count]);
    }

    private function getCount($userId, $ipAddress)
    {
        if ($userId) {
            return Wishlist::where('user_id', $userId)->count();
        }
        return Wishlist::whereNull('user_id')->where('ip_address', $ipAddress)->count();
    }
}
