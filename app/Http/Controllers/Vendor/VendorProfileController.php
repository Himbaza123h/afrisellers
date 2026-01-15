<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VendorProfileController extends Controller
{
    /**
     * Show vendor store settings and statistics
     */
    public function storeSettings()
    {
        try {
            $user = Auth::user();

            if (!$user->isVendor() || !$user->vendor) {
                return redirect()->route('dashboard')
                    ->with('error', 'You must be a vendor to access this page.');
            }

            $vendor = $user;
            $vendor->load(['businessProfile', 'plan']);

            // Get business statistics
            $stats = [
                'total_products' => $user->products()->count(),
                'active_products' => $user->products()->where('status', 'active')->count(),
                'pending_products' => $user->products()->where('status', 'pending')->count(),
                'total_views' => $user->products()->sum('views'),
                'total_inquiries' => 0, // Add inquiry relationship if exists
                'total_reviews' => \App\Models\ProductUserReview::whereIn('product_id', $user->products()->pluck('id'))->where('status', true)->count(),
                'average_rating' => \App\Models\ProductUserReview::whereIn('product_id', $user->products()->pluck('id'))->where('status', true)->avg('mark') ?? 0,
                'account_age_days' => $vendor->created_at->diffInDays(now()),
                'total_impressions' => \App\Models\Performance::where('vendor_id', $vendor->businessProfile->id)->sum('impressions'),
                'total_clicks' => \App\Models\Performance::where('vendor_id', $vendor->businessProfile->id)->sum('clicks'),
            ];

            return view('vendor.store.settings', compact('vendor', 'stats'));
        } catch (\Exception $e) {
            Log::error('Vendor Store Settings Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while loading store settings.');
        }
    }

    /**
     * Update vendor store settings
     */
    public function updateStoreSettings(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->isVendor() || !$user->vendor) {
                return redirect()->route('dashboard')
                    ->with('error', 'You must be a vendor to perform this action.');
            }

            $vendor = $user->vendor;
            $businessProfile = $vendor->businessProfile;

            if (!$businessProfile) {
                return back()->with('error', 'Business profile not found.');
            }

            $validated = $request->validate([
                'business_name' => 'required|string|max:255',
                'business_email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'phone_code' => 'nullable|string|max:10',
                'address' => 'required|string|max:500',
                'city' => 'nullable|string|max:255',
                'youtube_link' => [
                'nullable',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/'
                        ],
                'country_id' => 'nullable|exists:countries,id',
                'postal_code' => 'nullable|string|max:20',
                'website' => 'nullable|url|max:255',
                'description' => 'nullable|string|max:1000',
                'business_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Handle logo upload
            if ($request->hasFile('business_logo')) {
                // Delete old logo if exists
                if ($businessProfile->logo) {
                    Storage::disk('public')->delete($businessProfile->logo);
                }

                $logoPath = $request->file('business_logo')->store('business-logos', 'public');
                $validated['logo'] = $logoPath;
            }

            // Update business profile
            $businessProfile->update([
                'business_name' => $validated['business_name'],
                'business_email' => $validated['business_email'],
                'phone' => $validated['phone'],
                'phone_code' => $validated['phone_code'] ?? null,
                'address' => $validated['address'],
                'city' => $validated['city'] ?? null,
                'youtube_link' => $validated['youtube_link'] ?? null,
                'country_id' => $validated['country_id'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'website' => $validated['website'] ?? null,
                'description' => $validated['description'] ?? null,
                'logo' => $validated['logo'] ?? $businessProfile->logo,
            ]);

            Log::info('Vendor store settings updated', [
                'user_id' => $user->id,
                'vendor_id' => $vendor->id,
            ]);

            return redirect()
                ->route('vendor.store.settings')
                ->with('success', 'Store settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update vendor store settings', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update store settings. Please try again.']);
        }
    }
}
