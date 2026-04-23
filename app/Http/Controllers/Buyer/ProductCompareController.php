<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductCompareController extends Controller
{
    private const MAX   = 4;
    private const KEY   = 'compare_products';

    // ─── INDEX ────────────────────────────────────────────────────────
    public function index()
    {
        $ids      = session(self::KEY, []);
        $products = Product::whereIn('id', $ids)
            ->with([
                'prices'        => fn($q) => $q->orderedByQuantity(),
                'variations'    => fn($q) => $q->active(),
                'images'        => fn($q) => $q->primary()->orderBy('sort_order'),
                'productCategory',
                'country',
            ])
            ->get()
            ->sortBy(fn($p) => array_search($p->id, $ids))
            ->values();

        return view('buyer.compare.index', compact('products'));
    }

    // ─── ADD ──────────────────────────────────────────────────────────
public function add(Request $request, Product $product)
{
    $ids = session(self::KEY, []);

    if (in_array($product->id, $ids)) {
        return $this->redirectBack($request, 'info', 'Product is already in your comparison list.');
    }

    if (count($ids) >= self::MAX) {
        return $this->redirectBack($request, 'error', 'You can compare up to ' . self::MAX . ' products at a time.');
    }

    $ids[] = $product->id;
    session([self::KEY => $ids]);

    return $this->redirectBack($request, 'success', "\"{$product->name}\" added to comparison.");
}

    // ─── REMOVE ───────────────────────────────────────────────────────
public function remove(Request $request, Product $product)
{
    $ids = session(self::KEY, []);
    $ids = array_values(array_filter($ids, fn($id) => $id !== $product->id));
    session([self::KEY => $ids]);

    return $this->redirectBack($request, 'success', "\"{$product->name}\" removed from comparison.");
}

    // ─── CLEAR ────────────────────────────────────────────────────────
    public function clear()
    {
        session()->forget(self::KEY);
        return redirect()->route('buyer.compare.index')->with('success', 'Comparison list cleared.');
    }

    // ─── HELPER ───────────────────────────────────────────────────────
private function redirectBack(Request $request, string $type, string $msg)
{
    $url = $request->input('redirect') ?: url()->previous();
    return redirect($url)->with($type, $msg);
}
}
