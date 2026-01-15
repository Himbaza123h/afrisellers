<?php

namespace App\Http\Controllers\Vendor\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Vendor\Vendor;
use App\Models\ProductCategory;
use App\Models\Country;
use App\Models\ProductPriceTier;
use App\Models\ProductPrice;
use App\Models\ProductVariation;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Get the authenticated vendor.
     */
    private function getVendor()
    {
        $vendor = Vendor::with('businessProfile')
            ->where('user_id', auth()->id())
            ->first();

        if (!$vendor) {
            abort(403, 'Vendor profile not found.');
        }

        return $vendor;
    }

    /**
     * Display a listing of the vendor's products.
     */
    public function index(Request $request)
    {
        try {
            $vendor = $this->getVendor();

            $query = Product::with(['productCategory', 'country', 'images', 'prices'])
                ->where('user_id', auth()->id());

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('category')) {
                $query->where('product_category_id', $request->category);
            }

            if ($request->filled('date_range')) {
                $dateRange = $request->date_range;
                if (strpos($dateRange, ' to ') !== false) {
                    $dates = explode(' to ', $dateRange);
                    if (count($dates) === 2) {
                        try {
                            $start = \Carbon\Carbon::parse(trim($dates[0]))->startOfDay();
                            $end = \Carbon\Carbon::parse(trim($dates[1]))->endOfDay();
                            $query->whereBetween('created_at', [$start, $end]);
                        } catch (\Exception $e) {
                            Log::warning('Invalid date range format: ' . $dateRange);
                        }
                    }
                }
            }

            $categories = \App\Models\ProductCategory::where('status', 'active')
                ->orderBy('name')
                ->get();

            $products = $query->latest()->paginate(10)->withQueryString();

            return view('vendor.product.index', compact('products', 'vendor', 'categories'));
        } catch (\Exception $e) {
            Log::error('Vendor Product Index Error: ' . $e->getMessage());
            return redirect()->route('vendor.dashboard.home')
                ->with('error', 'An error occurred while loading products.');
        }
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $vendor = $this->getVendor();
        $categories = ProductCategory::where('status', 'active')->orderBy('name')->get();
        $countries = Country::where('status', 'active')->orderBy('name')->get();
        $promoCodes = \App\Models\PromoCode::active()->available()->orderBy('code')->get();

        $vendorCountryId = null;
        if ($vendor->businessProfile) {
            $vendorCountryId = $vendor->businessProfile->country_id;
        }

        return view('vendor.product.create', compact('categories', 'countries', 'vendor', 'vendorCountryId'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $vendor = $this->getVendor();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'country_id' => 'required|exists:countries,id',
            'overview' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'is_negotiable' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $validated['specifications'] = $this->parseJsonField($request->input('specifications'));
        $validated['user_id'] = auth()->id();
        $validated['is_admin_verified'] = false;
        $validated['is_negotiable'] = $request->has('is_negotiable') ? true : false;


        if (empty($validated['country_id']) && $vendor->businessProfile) {
            $validated['country_id'] = $vendor->businessProfile->country_id;
        }

        try {
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
                $baseSlug = $validated['slug'];
                $counter = 1;
                while (Product::where('slug', $validated['slug'])->exists()) {
                    $validated['slug'] = $baseSlug . '-' . $counter;
                    $counter++;
                }
            }

            $product = Product::create($validated);
            $this->syncVariations($product, $request->input('variations', []));
            $this->handleImages($product, $request);

            if ($request->filled('promo_codes')) {
                $product->promoCodes()->sync($request->input('promo_codes', []));
            }

            Log::info('Product created successfully', [
                'product_id' => $product->id,
                'name' => $product->name,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('vendor.product.show', $product)
                ->with('success', 'Product created successfully. You can now set pricing.');
        } catch (\Exception $e) {
            Log::error('Failed to create product', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to create product. Please try again.']);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $vendor = $this->getVendor();

        if ($product->user_id !== auth()->id()) {
            abort(403, 'You can only view your own products.');
        }

        $product->load(['productCategory', 'country', 'prices', 'variations', 'images', 'promoCodes']);
        return view('vendor.product.show', compact('product', 'vendor'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $vendor = $this->getVendor();

        if ($product->user_id !== auth()->id()) {
            abort(403, 'You can only edit your own products.');
        }

        $product->load(['prices', 'variations', 'images']);
        $categories = ProductCategory::where('status', 'active')->orderBy('name')->get();
        $countries = Country::where('status', 'active')->orderBy('namae')->get();
        $promoCodes = \App\Models\PromoCode::active()->available()->orderBy('code')->get();

        $vendorCountryId = null;
        if ($vendor->businessProfile) {
            $vendorCountryId = $vendor->businessProfile->country_id;
        }

        return view('vendor.product.edit', compact('product', 'categories', 'countries', 'vendor', 'vendorCountryId'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $vendor = $this->getVendor();

        if ($product->user_id !== auth()->id()) {
            abort(403, 'You can only update your own products.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'country_id' => 'required|exists:countries,id',
            'overview' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $validated['specifications'] = $this->parseJsonField($request->input('specifications'));
        $validated['user_id'] = auth()->id();

        $validated['is_negotiable'] = $request->has('is_negotiable') ? true : false;


        if (empty($validated['country_id']) && $vendor->businessProfile) {
            $validated['country_id'] = $vendor->businessProfile->country_id;
        }

        try {
            if (empty($validated['slug']) && $request->has('name')) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            $product->update($validated);
            $this->syncVariations($product, $request->input('variations', []));
            $this->handleImages($product, $request);

            if ($request->filled('promo_codes')) {
                $product->promoCodes()->sync($request->input('promo_codes', []));
            }

            Log::info('Product updated successfully', ['product_id' => $product->id]);
            return redirect()->route('vendor.product.show', $product)->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update product', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to update product. Please try again.']);
        }
    }


    /**
 * Show the form for editing product promo codes.
 */
public function editPromoCodes(Product $product)
{
    $vendor = $this->getVendor();

    if ($product->user_id !== auth()->id()) {
        abort(403, 'You can only edit promo codes for your own products.');
    }

    $product->load('promoCodes');
    $availablePromoCodes = \App\Models\PromoCode::active()->available()->orderBy('code')->get();
    $assignedPromoCodeIds = $product->promoCodes->pluck('id')->toArray();

    return view('vendor.product.promo-edit', compact('product', 'availablePromoCodes', 'assignedPromoCodeIds', 'vendor'));
}

/**
 * Update product promo codes.
 */
public function updatePromoCodes(Request $request, Product $product)
{
    $vendor = $this->getVendor();

    if ($product->user_id !== auth()->id()) {
        abort(403, 'You can only update promo codes for your own products.');
    }

    $validated = $request->validate([
        'promo_codes' => 'nullable|array',
        'promo_codes.*' => 'exists:promo_codes,id',
    ]);

    try {
        $promoCodeIds = $validated['promo_codes'] ?? [];
        $product->promoCodes()->sync($promoCodeIds);

        Log::info('Product promo codes updated successfully', [
            'product_id' => $product->id,
            'promo_codes_count' => count($promoCodeIds)
        ]);

        return redirect()->route('vendor.product.show', $product)
            ->with('success', 'Promo codes updated successfully.');
    } catch (\Exception $e) {
        Log::error('Failed to update product promo codes', ['error' => $e->getMessage()]);
        return back()->withInput()->withErrors(['error' => 'Failed to update promo codes. Please try again.']);
    }
}

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $vendor = $this->getVendor();

        if ($product->user_id !== auth()->id()) {
            abort(403, 'You can only delete your own products.');
        }

        try {
            $productName = $product->name;
            $product->delete();

            Log::info('Product deleted successfully', ['product_id' => $product->id]);
            return redirect()->route('vendor.product.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete product', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to delete product. Please try again.']);
        }
    }

    /**
     * Toggle product status.
     */
    public function toggleStatus(Product $product)
    {
        $vendor = $this->getVendor();

        if ($product->user_id !== auth()->id()) {
            abort(403, 'You can only toggle status of your own products.');
        }

        try {
            $statuses = ['draft' => 'active', 'active' => 'inactive', 'inactive' => 'draft'];
            $product->status = $statuses[$product->status] ?? 'active';
            $product->save();

            Log::info('Product status toggled', ['product_id' => $product->id, 'new_status' => $product->status]);
            return back()->with('success', 'Product status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to toggle product status', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to update product status. Please try again.']);
        }
    }

    /**
     * Show the form for editing product prices.
     */
    public function editPrice(Product $product)
    {
        $vendor = $this->getVendor();

        if ($product->user_id !== auth()->id()) {
            abort(403, 'You can only edit prices for your own products.');
        }

        $prices = $product->prices()->orderBy('min_qty')->get();

        return view('vendor.product.price-edit', compact('product', 'prices', 'vendor'));
    }

    /**
     * Update product prices.
     */
    public function updatePrice(Request $request, Product $product)
    {
        $vendor = $this->getVendor();

        if ($product->user_id !== auth()->id()) {
            abort(403, 'You can only update prices for your own products.');
        }

        $validated = $request->validate([
            'currency' => 'required|string|max:3',
            'prices' => 'required|array|min:1',
            'prices.*.min_qty' => 'required|integer|min:1',
            'prices.*.max_qty' => 'nullable|integer|min:1',
            'prices.*.price' => 'required|numeric|min:0.01',
            'prices.*.discount' => 'nullable|numeric|min:0',
            'prices.*.id' => 'nullable|exists:product_prices,id',
        ]);

        try {
            DB::beginTransaction();

            // Get IDs of prices to keep
            $keepIds = collect($validated['prices'])
                ->pluck('id')
                ->filter()
                ->toArray();

            // Delete prices not in the keep list
            $product->prices()
                ->whereNotIn('id', $keepIds)
                ->delete();

            // Update or create prices
            foreach ($validated['prices'] as $priceData) {
                $priceData['currency'] = $validated['currency'];
                $priceData['product_id'] = $product->id;

                if (!empty($priceData['id'])) {
                    // Update existing
                    ProductPrice::where('id', $priceData['id'])
                        ->where('product_id', $product->id)
                        ->update([
                            'min_qty' => $priceData['min_qty'],
                            'max_qty' => $priceData['max_qty'] ?? null,
                            'price' => $priceData['price'],
                            'discount' => $priceData['discount'] ?? 0,
                            'currency' => $priceData['currency'],
                        ]);
                } else {
                    // Create new
                    ProductPrice::create([
                        'product_id' => $product->id,
                        'min_qty' => $priceData['min_qty'],
                        'max_qty' => $priceData['max_qty'] ?? null,
                        'price' => $priceData['price'],
                        'currency' => $priceData['currency'],
                    ]);
                }
            }

            DB::commit();

            Log::info('Product prices updated successfully', ['product_id' => $product->id]);
            return redirect()->route('vendor.product.show', $product)
                ->with('success', 'Product pricing updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update product prices', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to update pricing. Please try again.']);
        }
    }

    /**
     * Helper: Parse JSON field from request.
     */
    private function parseJsonField($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        return is_array($value) ? $value : null;
    }

    /**
     * Helper: Sync variations for product.
     */
    private function syncVariations(Product $product, array $variations)
    {
        $product->variations()->delete();

        foreach ($variations as $variation) {
            if (!empty($variation['variation_type']) && !empty($variation['variation_name'])) {
                ProductVariation::create([
                    'product_id' => $product->id,
                    'variation_type' => $variation['variation_type'],
                    'variation_name' => $variation['variation_name'],
                    'variation_value' => $variation['variation_value'] ?? null,
                    'sort_order' => $variation['sort_order'] ?? 0,
                    'is_active' => $variation['is_active'] ?? true,
                ]);
            }
        }
    }

    /**
     * Helper: Handle product images (upload files and sync).
     */
    private function handleImages(Product $product, Request $request)
    {
        $existingImageIds = $request->input('existing_images', []);

        $product
            ->images()
            ->whereNotIn('id', $existingImageIds)
            ->each(function ($image) {
                $imagePath = parse_url($image->image_url, PHP_URL_PATH);
                $imagePath = str_replace('/public/storage/', '', $imagePath);
                $imagePath = ltrim($imagePath, '/');
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                if ($image->thumbnail_url && $image->thumbnail_url !== $image->image_url) {
                    $thumbnailPath = parse_url($image->thumbnail_url, PHP_URL_PATH);
                    $thumbnailPath = str_replace('/public/storage/', '', $thumbnailPath);
                    $thumbnailPath = ltrim($thumbnailPath, '/');
                    if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('public')->delete($thumbnailPath);
                    }
                }
                $image->delete();
            });

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                if ($file->isValid()) {
                    $path = $file->store('products/images', 'public');
                    $imageUrl = asset('public/storage/' . $path);
                    $thumbnailUrl = $imageUrl;

                    $sortOrder = $request->input("image_sort_order.{$index}", $product->images()->count() + $index);
                    $isPrimary = $request->input("image_is_primary.{$index}", false);

                    if (!$isPrimary && $product->images()->where('is_primary', true)->count() === 0 && $index === 0) {
                        $isPrimary = true;
                    }

                    if ($isPrimary) {
                        $product->images()->update(['is_primary' => false]);
                    }

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $imageUrl,
                        'thumbnail_url' => $thumbnailUrl,
                        'alt_text' => $request->input("image_alt_text.{$index}", $product->name),
                        'sort_order' => $sortOrder,
                        'is_primary' => $isPrimary,
                    ]);
                }
            }
        }

        $existingImageData = $request->input('existing_image_data', []);
        foreach ($existingImageData as $imageId => $data) {
            if (in_array($imageId, $existingImageIds)) {
                $image = ProductImage::find($imageId);
                if ($image && $image->product_id === $product->id) {
                    $isPrimary = isset($data['is_primary']) && $data['is_primary'];

                    if ($isPrimary) {
                        $product
                            ->images()
                            ->where('id', '!=', $imageId)
                            ->update(['is_primary' => false]);
                    }

                    $image->update([
                        'alt_text' => $data['alt_text'] ?? $image->alt_text,
                        'sort_order' => $data['sort_order'] ?? $image->sort_order,
                        'is_primary' => $isPrimary,
                    ]);
                }
            }
        }
    }
}
