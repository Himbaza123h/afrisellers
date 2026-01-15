<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use App\Models\ProductCart;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{

    public function index(Request $request)
{
    $userNumber = $request->cookie('user_number') ?? session('user_number');

    $cartItems = ProductCart::with(['product.images', 'product.prices'])
        ->where(function($query) use ($userNumber) {
            if (auth()->check()) {
                $query->where('user_id', auth()->id())
                      ->orWhere('user_number', $userNumber);
            } else {
                $query->where('user_number', $userNumber);
            }
        })
        ->latest()
        ->get();

    $totalAmount = $cartItems->sum(function($item) {
        return $item->price * $item->quantity;
    });

    // ADD THESE LINES
    $availableCars = Car::available()->get();
    // dd($availableCars);
    $shippingCost = session('shipping_cost', 0);
    $selectedCarId = session('shipping_car_id');

    return view('cart.index', compact('cartItems', 'totalAmount', 'availableCars', 'shippingCost', 'selectedCarId'));
}

    public function updateShipping(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'nullable|exists:cars,id',
        ]);

        if ($validated['car_id']) {
            $car = Car::findOrFail($validated['car_id']);
            session([
                'shipping_car_id' => $car->id,
                'shipping_cost' => $car->price ?? 0
            ]);
        } else {
            session()->forget(['shipping_car_id', 'shipping_cost']);
        }

        return response()->json([
            'success' => true,
            'shipping_cost' => session('shipping_cost', 0)
        ]);
    }

public function add(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'selected_variations' => 'nullable|string', // ← Changed from 'json'
    ]);

    $product = Product::with('prices')->findOrFail($validated['product_id']);

    // Get price based on quantity
    $priceInfo = $product->prices()
        ->where('min_qty', '<=', $validated['quantity'])
        ->where(function($query) use ($validated) {
            $query->whereNull('max_qty')
                  ->orWhere('max_qty', '>=', $validated['quantity']);
        })
        ->orderBy('min_qty', 'desc')
        ->first();

    if (!$priceInfo) {
        return back()->with('error', 'Price not available for this quantity');
    }

    $finalPrice = $priceInfo->price - $priceInfo->discount;

    // Get or create user number
    $userNumber = $request->cookie('user_number') ?? session('user_number');
    if (!$userNumber) {
        $userNumber = 'GUEST_' . time() . '_' . rand(1000, 9999);
        session(['user_number' => $userNumber]);
        cookie()->queue('user_number', $userNumber, 60 * 24 * 365);
    }

    // ✅ DECODE JSON STRING TO ARRAY BEFORE SAVING
    $variations = null;
    if (!empty($validated['selected_variations'])) {
        $variations = json_decode($validated['selected_variations'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON in selected_variations', [
                'input' => $validated['selected_variations'],
                'error' => json_last_error_msg()
            ]);
            $variations = null;
        }
    }

    Log::info('Processed Variations', [
        'raw' => $validated['selected_variations'] ?? null,
        'decoded' => $variations
    ]);

    // Check if item already exists
    $existingCart = ProductCart::where('product_id', $validated['product_id'])
        ->where(function($query) use ($userNumber) {
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            } else {
                $query->where('user_number', $userNumber);
            }
        })
        ->first();

    if ($existingCart) {
        $existingCart->update([
            'quantity' => $existingCart->quantity + $validated['quantity'],
            'price' => $finalPrice,
            'selected_variations' => $variations // ← Pass array, not string
        ]);
    } else {
        ProductCart::create([
            'product_id' => $validated['product_id'],
            'user_id' => auth()->id(),
            'user_number' => $userNumber,
            'quantity' => $validated['quantity'],
            'price' => $finalPrice,
            'currency' => $priceInfo->currency,
            'selected_variations' => $variations // ← Pass array, not string
        ]);
    }

    // Get updated cart count
    $cartCount = ProductCart::where(function($query) use ($userNumber) {
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $query->where('user_number', $userNumber);
        }
    })->sum('quantity');

    return back()->with('success', "✅ Product added to cart! You now have {$cartCount} item(s).");
}

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = ProductCart::findOrFail($id);
        $cartItem->update(['quantity' => $validated['quantity']]);

        return back()->with('success', 'Cart updated successfully!');
    }

    public function remove($id)
    {
        $cartItem = ProductCart::findOrFail($id);
        $cartItem->delete();

        return back()->with('success', 'Item removed from cart!');
    }

    public function count(Request $request)
    {
        $userNumber = $request->cookie('user_number') ?? session('user_number');

        $count = ProductCart::where(function($query) use ($userNumber) {
            if (auth()->check()) {
                $query->where('user_id', auth()->id())
                      ->orWhere('user_number', $userNumber);
            } else {
                $query->where('user_number', $userNumber);
            }
        })->sum('quantity');

        return response()->json(['count' => $count]);
    }
}
