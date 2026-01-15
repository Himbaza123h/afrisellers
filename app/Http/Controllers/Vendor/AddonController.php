<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\AddonUser;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AddonController extends Controller
{
    /**
     * Display a listing of the vendor's addons.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Build query with filters
        $query = AddonUser::where('user_id', $user->id)
            ->with(['addon', 'addon.country']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('addon', function($addonQuery) use ($search) {
                    $addonQuery->where('locationX', 'like', "%{$search}%")
                        ->orWhere('locationY', 'like', "%{$search}%");
                })->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'expired') {
                $query->expired();
            } elseif ($request->status === 'pending') {
                $query->whereNull('paid_at');
            }
        }

        // Date range filter
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) === 2) {
                $query->whereBetween('paid_at', [$dates[0], $dates[1]]);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $addonUsers = $query->paginate(15);

        // Calculate statistics
        $stats = [
            'total' => AddonUser::where('user_id', $user->id)->count(),
            'active' => AddonUser::where('user_id', $user->id)->active()->count(),
            'expired' => AddonUser::where('user_id', $user->id)->expired()->count(),
            'pending' => AddonUser::where('user_id', $user->id)->whereNull('paid_at')->count(),
        ];

        // Calculate percentages
        $stats['active_percentage'] = $stats['total'] > 0
            ? round(($stats['active'] / $stats['total']) * 100)
            : 0;
        $stats['expired_percentage'] = $stats['total'] > 0
            ? round(($stats['expired'] / $stats['total']) * 100)
            : 0;

        // Calculate total spent
        $stats['total_spent'] = AddonUser::where('user_id', $user->id)
            ->whereNotNull('paid_at')
            ->join('addons', 'addon_users.addon_id', '=', 'addons.id')
            ->sum('addons.price');

        // Calculate pending amount (active addons value)
        $stats['active_value'] = AddonUser::where('user_id', $user->id)
            ->active()
            ->join('addons', 'addon_users.addon_id', '=', 'addons.id')
            ->sum('addons.price');

        return view('vendor.addons.index', compact('addonUsers', 'stats'));
    }

    /**
     * Display available addons for purchase.
     */
    public function available()
    {
        $user = auth()->user();

        // Get user's business profile to determine country
        $businessProfile = BusinessProfile::where('user_id', $user->id)->first();

        if (!$businessProfile || !$businessProfile->country_id) {
            return redirect()->route('vendor.addons.index')
                ->with('error', 'Please complete your business profile with country information first.');
        }

        // Get available addons for user's country or global addons
        $availableAddons = Addon::where(function($query) use ($businessProfile) {
            $query->where('country_id', $businessProfile->country_id)
                  ->orWhereNull('country_id'); // Global addons available to all
        })
        ->with('country')
        ->latest()
        ->paginate(12);

        // Get vendor's current active addon IDs
        $userAddonIds = AddonUser::where('user_id', $user->id)
            ->active()
            ->pluck('addon_id')
            ->toArray();

        return view('vendor.addons.available', compact('availableAddons', 'userAddonIds', 'businessProfile'));
    }

    /**
     * Show the form for purchasing an addon.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $addonId = $request->get('addon_id');

        if (!$addonId) {
            return redirect()->route('vendor.addons.available')
                ->with('error', 'Please select an addon to purchase.');
        }

        $addon = Addon::with('country')->findOrFail($addonId);

        // Get user's business profile
        $businessProfile = BusinessProfile::where('user_id', $user->id)->first();

        // Check if addon is available for user's country
        if ($addon->country_id && $addon->country_id !== $businessProfile->country_id) {
            return redirect()->route('vendor.addons.available')
                ->with('error', 'This addon is not available in your country.');
        }

        $products = $user->products()->get();
        $showrooms = $user->vendor?->showrooms ?? collect();
        $suppliers = BusinessProfile::where('user_id', $user->id)->get();

        return view('vendor.addons.create', compact('addon', 'products', 'showrooms'));
    }

    /**
     * Purchase a new addon.
     */
    public function purchase(Request $request, $addonId)
    {


        $request->validate([
            'type' => 'required|in:product,supplier,loadboad,car,showroom,tradeshow',
            'related_id' => 'required_unless:type,supplier|nullable|integer',
            'duration_days' => 'required|integer|min:1|max:365',
        ]);

        $addon = Addon::findOrFail($addonId);
        $user = auth()->user();

        // Get business profile
        $businessProfile = BusinessProfile::where('user_id', $user->id)->first();

        // Verify addon availability
        if ($addon->country_id && $addon->country_id !== $businessProfile->country_id) {
            return back()->with('error', 'This addon is not available in your country.');
        }

        // Check if user already has an active addon for this item
        $existingAddon = AddonUser::where('user_id', $user->id)
            ->where('addon_id', $addonId)
            ->where('type', $request->type)
            ->where($request->type . '_id', $request->related_id)
            ->active()
            ->first();

        if ($existingAddon) {
            return back()->with('error', 'You already have an active addon for this item.');
        }

        // Calculate total price based on duration (assuming price is per 30 days)
        $totalPrice = $addon->price * ($request->duration_days / 30);

        // TODO: Process payment here
        // ... payment logic ...

    try {
        // Determine the related_id based on type
        if ($request->type === 'supplier') {
            $relatedId = $user->vendor->id;
        } else {
            $relatedId = $request->related_id;
        }

        // Create addon user record
        $addonUser = new AddonUser([
            'addon_id' => $addonId,
            'user_id' => $user->id,
            'type' => $request->type,
            'paid_at' => now(),
            'paid_days' => $request->duration_days,
            $request->type . '_id' => $relatedId,
        ]);

        $addonUser->save();
        $addonUser->calculateEndDate();

        return redirect()->route('vendor.addons.index')
            ->with('success', 'Addon purchased successfully! Your item will now be featured.');
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to purchase addon: ' . $e->getMessage());
    }
    }

    /**
     * Display the specified addon.
     */
    public function show($id)
    {
        $addonUser = AddonUser::where('user_id', auth()->id())
            ->with(['addon', 'addon.country', 'product', 'supplier', 'showroom', 'tradeshow'])
            ->findOrFail($id);

        return view('vendor.addons.show', compact('addonUser'));
    }

    /**
     * Show the form for renewing an addon.
     */
    public function renewForm($id)
    {
        $addonUser = AddonUser::where('user_id', auth()->id())
            ->with(['addon', 'addon.country'])
            ->findOrFail($id);

        return view('vendor.addons.renew', compact('addonUser'));
    }

    /**
     * Renew an addon subscription.
     */
    public function renew(Request $request, $id)
    {
        $request->validate([
            'duration_days' => 'required|integer|min:1|max:365',
        ]);

        $addonUser = AddonUser::where('user_id', auth()->id())->findOrFail($id);
        $addon = $addonUser->addon;

        // Calculate renewal price
        $renewalPrice = $addon->price * ($request->duration_days / 30);

        // TODO: Process payment here
        // ... payment logic ...

    try {
        $addonUser->renew((int) $request->duration_days);

        return redirect()->route('vendor.addons.index')
            ->with('success', 'Addon renewed successfully for ' . $request->duration_days . ' days!');
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to renew addon: ' . $e->getMessage());
    }
    }

    /**
     * Deactivate an addon.
     */
    public function deactivate($id)
    {
        $addonUser = AddonUser::where('user_id', auth()->id())->findOrFail($id);

        // Set ended_at to now to effectively deactivate it
        $addonUser->update(['ended_at' => now()]);

        return back()->with('success', 'Addon deactivated successfully!');
    }

    /**
     * Cancel an addon subscription.
     */
    public function cancel($id)
    {
        $addonUser = AddonUser::where('user_id', auth()->id())->findOrFail($id);

        // Soft delete the addon user
        $addonUser->delete();

        return back()->with('success', 'Addon cancelled successfully!');
    }

    /**
     * Download invoice for addon.
     */
    public function invoice($id)
    {
        $addonUser = AddonUser::where('user_id', auth()->id())
            ->with(['addon', 'addon.country', 'user'])
            ->findOrFail($id);

        return view('vendor.addons.invoice', compact('addonUser'));
    }
}
