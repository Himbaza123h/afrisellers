<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Models\ShowroomProduct;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VendorShowroomController extends Controller
{
    /**
     * Display a listing of vendor's showrooms
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user->isVendor()) {
                return redirect()->route('dashboard')
                    ->with('error', 'You must be a vendor to access this page.');
            }

            $showrooms = Showroom::where('user_id', $user->id)
                ->withCount(['products'])
                ->latest()
                ->paginate(10);

            return view('vendor.showrooms.index', compact('showrooms'));
        } catch (\Exception $e) {
            Log::error('Vendor Showrooms List Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load showrooms.');
        }
    }

    /**
     * Show the form for creating a new showroom
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->isVendor()) {
            return redirect()->route('dashboard')
                ->with('error', 'You must be a vendor to access this page.');
        }

        $countries = Country::where('status', 'active')->orderBy('name')->get();

        return view('vendor.showrooms.create', compact('countries'));
    }

    /**
     * Store a newly created showroom
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->isVendor()) {
                return redirect()->route('dashboard')
                    ->with('error', 'You must be a vendor to perform this action.');
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:2000',
                'business_type' => 'nullable|string|max:100',
                'industry' => 'nullable|string|max:100',
                'country_id' => 'required|exists:countries,id',
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:255',
                'state_province' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'alternate_phone' => 'nullable|string|max:20',
                'whatsapp' => 'nullable|string|max:20',
                'website_url' => 'nullable|url|max:255',
                'contact_person' => 'required|string|max:255',
                'primary_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'logo_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Handle image uploads
            if ($request->hasFile('primary_image')) {
                $validated['primary_image'] = $request->file('primary_image')
                    ->store('showrooms/primary', 'public');
            }

            if ($request->hasFile('logo_image')) {
                $validated['logo_image'] = $request->file('logo_image')
                    ->store('showrooms/logos', 'public');
            }

            $images = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $images[] = $image->store('showrooms/gallery', 'public');
                }
            }
            $validated['images'] = $images;

            $validated['user_id'] = $user->id;
            $validated['slug'] = Str::slug($validated['name']);
            $validated['status'] = 'active';

            $showroom = Showroom::create($validated);

            Log::info('Showroom created', [
                'user_id' => $user->id,
                'showroom_id' => $showroom->id,
            ]);

            return redirect()
                ->route('vendor.showrooms.show', $showroom->id)
                ->with('success', 'Showroom created successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to create showroom', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create showroom. Please try again.']);
        }
    }


/**
 * Print showrooms report.
 */
public function print(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user->isVendor()) {
            return redirect()->route('dashboard')
                ->with('error', 'You must be a vendor to access this page.');
        }

        $showrooms = Showroom::where('user_id', $user->id)
            ->withCount(['products'])
            ->with('country')
            ->latest()
            ->get();

        // Calculate statistics
        $stats = [
            'total_showrooms' => $showrooms->count(),
            'total_products' => $showrooms->sum('products_count'),
            'total_views' => $showrooms->sum('views_count'),
            'total_inquiries' => $showrooms->sum('inquiries_count'),
            'active_showrooms' => $showrooms->where('status', 'active')->count(),
            'verified_showrooms' => $showrooms->where('is_verified', true)->count(),
            'featured_showrooms' => $showrooms->where('is_featured', true)->count(),
        ];

        // Country distribution - FIXED: pass $stats using 'use'
        $countryDistribution = $showrooms->groupBy('country_id')->map(function ($countryShowrooms) use ($stats) {
            return [
                'country' => $countryShowrooms->first()->country ?? null,
                'count' => $count = $countryShowrooms->count(),
                'percentage' => $stats['total_showrooms'] > 0 ? round(($count / $stats['total_showrooms']) * 100, 1) : 0
            ];
        })->sortByDesc('count');

        // Status distribution - FIXED: pass $stats using 'use'
        $statusDistribution = $showrooms->groupBy('status')->map(function ($statusShowrooms, $status) use ($stats) {
            return [
                'status' => $status,
                'count' => $count = $statusShowrooms->count(),
                'percentage' => $stats['total_showrooms'] > 0 ? round(($count / $stats['total_showrooms']) * 100, 1) : 0
            ];
        });

        return view('vendor.showrooms.print', compact(
            'showrooms',
            'stats',
            'countryDistribution',
            'statusDistribution'
        ));
    } catch (\Exception $e) {
        Log::error('Showrooms Print Error: ' . $e->getMessage());
        abort(500, 'An error occurred while generating the print report.');
    }
}
    /**
     * Display the specified showroom
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            $showroom = Showroom::where('user_id', $user->id)
                ->with(['country', 'products'])
                ->findOrFail($id);

            $products = $showroom->products()
                ->with(['images', 'productCategory'])
                ->paginate(12);

            $stats = [
                'total_products' => $showroom->products()->count(),
                'views_count' => $showroom->views_count ?? 0,
                'inquiries_count' => $showroom->inquiries_count ?? 0,
            ];

            return view('vendor.showrooms.show', compact('showroom', 'products', 'stats'));
        } catch (\Exception $e) {
            Log::error('Showroom View Error: ' . $e->getMessage());
            return redirect()->route('vendor.showrooms.index')
                ->with('error', 'Showroom not found.');
        }
    }

    /**
     * Show the form for editing the specified showroom
     */
    public function edit($id)
    {
        $user = Auth::user();

        $showroom = Showroom::where('user_id', $user->id)->findOrFail($id);
        $countries = Country::where('status', 'active')->orderBy('name')->get();

        return view('vendor.showrooms.edit', compact('showroom', 'countries'));
    }

    /**
     * Update the specified showroom
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            $showroom = Showroom::where('user_id', $user->id)->findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:2000',
                'business_type' => 'nullable|string|max:100',
                'industry' => 'nullable|string|max:100',
                'country_id' => 'required|exists:countries,id',
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:255',
                'state_province' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'alternate_phone' => 'nullable|string|max:20',
                'whatsapp' => 'nullable|string|max:20',
                'website_url' => 'nullable|url|max:255',
                'contact_person' => 'required|string|max:255',
                'primary_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'logo_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Handle primary image upload
            if ($request->hasFile('primary_image')) {
                if ($showroom->primary_image) {
                    Storage::disk('public')->delete($showroom->primary_image);
                }
                $validated['primary_image'] = $request->file('primary_image')
                    ->store('showrooms/primary', 'public');
            }

            // Handle logo upload
            if ($request->hasFile('logo_image')) {
                if ($showroom->logo_image) {
                    Storage::disk('public')->delete($showroom->logo_image);
                }
                $validated['logo_image'] = $request->file('logo_image')
                    ->store('showrooms/logos', 'public');
            }

            $validated['slug'] = Str::slug($validated['name']);

            $showroom->update($validated);

            return redirect()
                ->route('vendor.showrooms.show', $showroom->id)
                ->with('success', 'Showroom updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update showroom', [
                'showroom_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update showroom.']);
        }
    }

    /**
     * Manage showroom gallery
     */
    public function gallery($id)
    {
        $user = Auth::user();
        $showroom = Showroom::where('user_id', $user->id)->findOrFail($id);

        return view('vendor.showrooms.gallery', compact('showroom'));
    }

    /**
     * Upload images to showroom gallery
     */
    public function uploadGalleryImages(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $showroom = Showroom::where('user_id', $user->id)->findOrFail($id);

            $request->validate([
                'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $images = $showroom->images ?? [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $images[] = $image->store('showrooms/gallery', 'public');
                }
            }

            $showroom->update(['images' => $images]);

            return back()->with('success', 'Images uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Gallery Upload Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to upload images.');
        }
    }

    /**
     * Delete image from gallery
     */
    public function deleteGalleryImage(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $showroom = Showroom::where('user_id', $user->id)->findOrFail($id);

            $imageToDelete = $request->input('image');
            $images = $showroom->images ?? [];

            if (($key = array_search($imageToDelete, $images)) !== false) {
                Storage::disk('public')->delete($imageToDelete);
                unset($images[$key]);
                $showroom->update(['images' => array_values($images)]);
            }

            return back()->with('success', 'Image deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Gallery Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete image.');
        }
    }

    /**
     * Show products management for showroom
     */
    public function products($id)
    {
        $user = Auth::user();
        $showroom = Showroom::where('user_id', $user->id)->findOrFail($id);

        // Get products already in showroom
        $showroomProducts = $showroom->products()->with(['images', 'productCategory'])->paginate(12);

        // Get available products (user's products not in this showroom)
        $availableProducts = $user->products()
            ->whereNotIn('id', $showroom->products()->pluck('products.id'))
            ->with(['images', 'productCategory'])
            ->where('status', 'active')
            ->get();

        return view('vendor.showrooms.products', compact('showroom', 'showroomProducts', 'availableProducts'));
    }

    /**
     * Add product to showroom
     */
    public function addProduct(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $showroom = Showroom::where('user_id', $user->id)->findOrFail($id);

            $request->validate([
                'product_id' => 'required|exists:products,id',
            ]);

            $productId = $request->input('product_id');

            // Verify user owns the product
            $product = $user->products()->findOrFail($productId);

            // Check if product already in showroom
            if ($showroom->products()->where('products.id', $productId)->exists()) {
                return back()->with('error', 'Product is already in this showroom.');
            }

            ShowroomProduct::create([
                'showroom_id' => $showroom->id,
                'product_id' => $productId,
                'user_id' => $user->id,
                'added_at' => now(),
            ]);

            return back()->with('success', 'Product added to showroom successfully!');
        } catch (\Exception $e) {
            Log::error('Add Product to Showroom Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to add product to showroom.');
        }
    }

    /**
     * Remove product from showroom
     */

    public function removeProduct(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $showroom = Showroom::where('user_id', $user->id)->findOrFail($id);

            $request->validate([
                'product_id' => 'required|exists:products,id',
            ]);

            $productId = $request->input('product_id');

            // Hard delete - permanently remove from database
            ShowroomProduct::where('showroom_id', $showroom->id)
                ->where('product_id', $productId)
                ->where('user_id', $user->id)
                ->forceDelete();

            return back()->with('success', 'Product removed from showroom successfully!');
        } catch (\Exception $e) {
            Log::error('Remove Product from Showroom Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to remove product from showroom.');
        }
    }

    /**
     * Delete showroom
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $showroom = Showroom::where('user_id', $user->id)->findOrFail($id);

            // Delete images
            if ($showroom->primary_image) {
                Storage::disk('public')->delete($showroom->primary_image);
            }
            if ($showroom->logo_image) {
                Storage::disk('public')->delete($showroom->logo_image);
            }
            if ($showroom->images) {
                foreach ($showroom->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $showroom->delete();

            return redirect()
                ->route('vendor.showrooms.index')
                ->with('success', 'Showroom deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Delete Showroom Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete showroom.');
        }
    }
}
