<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\AgentCredit;
use App\Models\Credit;
use App\Models\CreditTransaction;

class VendorProductController extends Controller
{
    private function getVendor(int $vendorId): Vendor
    {
        return Vendor::where('agent_id', auth()->id())
            ->with(['user', 'businessProfile'])
            ->findOrFail($vendorId);
    }

    // ─── INDEX ────────────────────────────────────────────────────────
    public function index(Request $request, $vendorId)
    {
        $vendor = $this->getVendor($vendorId);

        $products = Product::where('user_id', $vendor->user_id)
            ->with(['images' => fn($q) => $q->orderBy('sort_order'), 'productCategory', 'country'])
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
            )
            ->when($request->status, fn($q) =>
                $q->where('status', $request->status)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'    => Product::where('user_id', $vendor->user_id)->count(),
            'active'   => Product::where('user_id', $vendor->user_id)->where('status', 'active')->count(),
            'inactive' => Product::where('user_id', $vendor->user_id)->where('status', 'inactive')->count(),
            'draft'    => Product::where('user_id', $vendor->user_id)->where('status', 'draft')->count(),
        ];

        return view('agent.vendors.products.index', compact('vendor', 'products', 'stats'));
    }

    // ─── SHOW ─────────────────────────────────────────────────────────
    public function show($vendorId, $productId)
    {
        $vendor  = $this->getVendor($vendorId);
        $product = Product::where('user_id', $vendor->user_id)
                    ->with(['images' => fn($q) => $q->orderBy('sort_order'), 'productCategory', 'country'])
                    ->findOrFail($productId);

        return view('agent.vendors.products.show', compact('vendor', 'product'));
    }

    // ─── CREATE ───────────────────────────────────────────────────────
    public function create($vendorId)
    {
        $vendor     = $this->getVendor($vendorId);
        $countries  = Country::orderBy('name')->get();
        $categories = ProductCategory::orderBy('name')->get();

        return view('agent.vendors.products.create', compact('vendor', 'countries', 'categories'));
    }

    // ─── STORE ────────────────────────────────────────────────────────
    public function store(Request $request, $vendorId)
    {
        $vendor = $this->getVendor($vendorId);

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'short_description'   => 'nullable|string|max:500',
            'description'         => 'nullable|string',
            'country_id'          => 'nullable|exists:countries,id',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'min_order_quantity'  => 'nullable|integer|min:1',
            'is_negotiable'       => 'nullable',
            'status'              => 'required|in:active,inactive,draft',
            'video'               => 'nullable|file|mimes:mp4,webm,mov|max:51200',
            'images.*'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Unique slug
        $base = Str::slug($validated['name']);
        $slug = $base;
        $i    = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        // Video upload
        $videoUrl = null;
        if ($request->hasFile('video')) {
            $videoUrl = $request->file('video')->store('products/videos', 'public');
        }

        $product = Product::create([
            'user_id'             => $vendor->user_id,
            'name'                => $validated['name'],
            'slug'                => $slug,
            'short_description'   => $validated['short_description'] ?? null,
            'description'         => $validated['description'] ?? null,
            'country_id'          => $validated['country_id'] ?? null,
            'product_category_id' => $validated['product_category_id'] ?? null,
            'min_order_quantity'  => $validated['min_order_quantity'] ?? 1,
            'is_negotiable'       => $request->boolean('is_negotiable'),
            'status'              => $validated['status'],
            'video_url'           => $videoUrl,
        ]);

        // Images
        if ($request->hasFile('images')) {
            $isPrimary = true;
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url'  => $path,
                    'is_primary' => $isPrimary,
                    'sort_order' => $index,
                    'alt_text'   => $product->name,
                ]);
                $isPrimary = false;
            }
        }

// ── Award credits to agent for product creation ────────────────
try {
    $creditEntry  = Credit::where('type', 'product_created')->first();
    $creditAmount = $creditEntry ? (float) $creditEntry->value : 5.0;

    $agentCredit = AgentCredit::firstOrNew(['agent_id' => auth()->id()]);
    $agentCredit->total_credits = (float) ($agentCredit->total_credits ?? 0) + $creditAmount;
    $agentCredit->save();

    CreditTransaction::create([
        'agent_id'         => auth()->id(),
        'transaction_type' => 'product_created',
        'credits'          => $creditAmount,
    ]);
} catch (\Throwable $e) {
    \Log::error('Credit award failed for agent ' . auth()->id() . ': ' . $e->getMessage());
}

return redirect()->route('agent.vendors.products.index', $vendorId)
    ->with('success', "Product <strong>{$product->name}</strong> created successfully.");
    }

    // ─── EDIT ─────────────────────────────────────────────────────────
    public function edit($vendorId, $productId)
    {
        $vendor     = $this->getVendor($vendorId);
        $product    = Product::where('user_id', $vendor->user_id)
                        ->with(['images' => fn($q) => $q->orderBy('sort_order'), 'productCategory', 'country'])
                        ->findOrFail($productId);
        $countries  = Country::orderBy('name')->get();
        $categories = ProductCategory::orderBy('name')->get();

        return view('agent.vendors.products.edit',
            compact('vendor', 'product', 'countries', 'categories'));
    }

    // ─── UPDATE ───────────────────────────────────────────────────────
    public function update(Request $request, $vendorId, $productId)
    {
        $vendor  = $this->getVendor($vendorId);
        $product = Product::where('user_id', $vendor->user_id)
                     ->with('images')
                     ->findOrFail($productId);

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'short_description'   => 'nullable|string|max:500',
            'description'         => 'nullable|string',
            'country_id'          => 'nullable|exists:countries,id',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'min_order_quantity'  => 'nullable|integer|min:1',
            'is_negotiable'       => 'nullable',
            'status'              => 'required|in:active,inactive,draft',
            'video'               => 'nullable|file|mimes:mp4,webm,mov|max:51200',
            'remove_video'        => 'nullable',
            'images.*'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'delete_images.*'     => 'nullable|integer|exists:product_images,id',
            'delete_all_images'   => 'nullable',
        ]);

        // Handle video
        $videoUrl = $product->video_url;
        if ($request->boolean('remove_video') && $videoUrl) {
            Storage::disk('public')->delete($videoUrl);
            $videoUrl = null;
        }
        if ($request->hasFile('video')) {
            if ($videoUrl) {
                Storage::disk('public')->delete($videoUrl);
            }
            $videoUrl = $request->file('video')->store('products/videos', 'public');
        }

        $product->update([
            'name'                => $validated['name'],
            'short_description'   => $validated['short_description'] ?? null,
            'description'         => $validated['description'] ?? null,
            'country_id'          => $validated['country_id'] ?? null,
            'product_category_id' => $validated['product_category_id'] ?? null,
            'min_order_quantity'  => $validated['min_order_quantity'] ?? 1,
            'is_negotiable'       => $request->boolean('is_negotiable'),
            'status'              => $validated['status'],
            'video_url'           => $videoUrl,
        ]);

        // Delete all images
        if ($request->boolean('delete_all_images')) {
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->getRawOriginal('image_url'));
                $img->delete();
            }
            $product->load('images');
        } elseif ($request->filled('delete_images')) {
            // Delete selected images
            $toDelete = ProductImage::whereIn('id', $request->delete_images)
                ->where('product_id', $product->id)->get();
            foreach ($toDelete as $img) {
                Storage::disk('public')->delete($img->getRawOriginal('image_url'));
                $img->delete();
            }
            $product->load('images');
        }

        // Fix primary if it got deleted
        if ($product->images->isNotEmpty() && !$product->images->firstWhere('is_primary', true)) {
            $product->images->first()->update(['is_primary' => true]);
        }

        // Upload new images
        if ($request->hasFile('images')) {
            $hasPrimary = $product->images()->where('is_primary', true)->exists();
            $nextOrder  = ($product->images()->max('sort_order') ?? 0) + 1;
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url'  => $path,
                    'is_primary' => !$hasPrimary,
                    'sort_order' => $nextOrder++,
                    'alt_text'   => $product->name,
                ]);
                $hasPrimary = true;
            }
        }

        return redirect()->route('agent.vendors.products.index', $vendorId)
            ->with('success', "Product <strong>{$product->name}</strong> updated successfully.");
    }

    // ─── DESTROY ──────────────────────────────────────────────────────
    public function destroy($vendorId, $productId)
    {
        $vendor  = $this->getVendor($vendorId);
        $product = Product::where('user_id', $vendor->user_id)
                     ->with('images')->findOrFail($productId);

        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->getRawOriginal('image_url'));
        }
        if ($product->video_url) {
            Storage::disk('public')->delete($product->video_url);
        }
        $product->images()->delete();
        $product->delete();

        return back()->with('success', 'Product deleted successfully.');
    }
}
