<?php

namespace App\Http\Controllers\Admin\BusinessProfile;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\OwnerID;
use App\Models\Role;
use App\Models\Vendor\Vendor;
use App\Models\Plan;
use App\Mail\BusinessProfileRejectionMail;
use App\Mail\BusinessProfileVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BusinessProfileController extends Controller
{

/**
 * Display a listing of business profiles.
 */
public function index(Request $request)
{
    $query = BusinessProfile::with(['user', 'country']);

    // Filter by status
    $filter = $request->get('filter', '');
    if ($filter === 'pending') {
        $query->where('is_admin_verified', false)
              ->where('verification_status', 'pending');
    } elseif ($filter === 'verified') {
        $query->where('is_admin_verified', true)
              ->where('verification_status', 'verified');
    } elseif ($filter === 'rejected') {
        $query->where('verification_status', 'rejected');
    }

    // Search filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('business_name', 'like', "%{$search}%")
              ->orWhereHas('user', function($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    // Country filter
    if ($request->filled('country')) {
        $query->where('country_id', $request->country);
    }

    // Date range filter
    if ($request->filled('date_range')) {
        $dateRange = $request->date_range;
        if ($dateRange === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($dateRange === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($dateRange === 'month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }
    }

    // Handle sorting
    $sortBy = $request->get('sort_by', 'created_at');
    $sortOrder = $request->get('sort_order', 'desc');

    switch ($sortBy) {
        case 'business_name':
            $query->orderBy('business_name', $sortOrder);
            break;
        case 'verification_status':
            $query->orderBy('verification_status', $sortOrder);
            break;
        default:
            $query->orderBy('created_at', $sortOrder);
    }

    $businessProfiles = $query->paginate(15)->withQueryString();

    // Get all countries for filter dropdown
    $countries = \App\Models\Country::orderBy('name')->get();

    // Calculate statistics
    $total = BusinessProfile::count();
    $pending = BusinessProfile::where('is_admin_verified', false)
        ->where('verification_status', 'pending')
        ->count();
    $verified = BusinessProfile::where('is_admin_verified', true)
        ->where('verification_status', 'verified')
        ->count();
    $rejected = BusinessProfile::where('verification_status', 'rejected')
        ->count();

    // Time-based stats
    $today = BusinessProfile::whereDate('created_at', today())->count();
    $thisWeek = BusinessProfile::whereBetween('created_at', [
        now()->startOfWeek(),
        now()->endOfWeek()
    ])->count();
    $thisMonth = BusinessProfile::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    $stats = [
        'total' => $total,
        'pending' => $pending,
        'verified' => $verified,
        'rejected' => $rejected,
        'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 1) : 0,
        'verified_percentage' => $total > 0 ? round(($verified / $total) * 100, 1) : 0,
        'today' => $today,
        'this_week' => $thisWeek,
        'this_month' => $thisMonth,
    ];

    return view('admin.business-profile.index', compact(
        'businessProfiles',
        'countries',
        'stats'
    ));
}

    /**
     * Show the specified business profile.
     */
    public function show(BusinessProfile $businessProfile)
    {
        $businessProfile->load(['user', 'country']);
        $ownerID = OwnerID::where('user_id', $businessProfile->user_id)->first();

        return view('admin.business-profile.show', compact('businessProfile', 'ownerID'));
    }

    /**
     * Verify the business profile and create vendor.
     */
    public function verify(Request $request, BusinessProfile $businessProfile)
    {
        try {
            DB::beginTransaction();

            // Update business profile
            $businessProfile->update([
                'is_admin_verified' => true,
                'verification_status' => 'verified',
            ]);

            // Get the default plan (or first plan if no default exists)
            $defaultPlan = Plan::where('is_default', true)->first();
            if (!$defaultPlan) {
                $defaultPlan = Plan::orderBy('id', 'asc')->first();
            }

            // Create vendor
            $vendor = $businessProfile->createVendorOnVerification();

            if (!$vendor) {
                // Get owner ID document for this user
                $ownerID = OwnerID::where('user_id', $businessProfile->user_id)->first();

                // Create vendor manually if method didn't create it
                // Generate email verification token (6 digits)
                $verificationToken = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                $vendor = Vendor::create([
                    'user_id' => $businessProfile->user_id,
                    'business_profile_id' => $businessProfile->id,
                    'owner_id_document_id' => $ownerID?->id,
                    'plan_id' => $defaultPlan?->id,
                    'email_verification_token' => $verificationToken,
                    'email_verified_at' => now(),
                    'account_status' => 'active',
                    'email_verified' => true,
                ]);

                $businessProfile->update(['vendor_id' => $vendor->id]);

                // Assign vendor role to user
                $vendorRole = Role::where('slug', 'vendor')->first();
                if ($vendorRole && !$businessProfile->user->hasRole('vendor')) {
                    $businessProfile->user->assignRole($vendorRole);
                }
            } else {
                // Update vendor with plan_id and verification fields if it was created by createVendorOnVerification
                // Generate email verification token if not already set (6 digits)
                if (!$vendor->email_verification_token) {
                    $verificationToken = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                } else {
                    $verificationToken = $vendor->email_verification_token;
                }

                $vendor->update([
                    'plan_id' => $defaultPlan?->id,
                    'email_verification_token' => $verificationToken,
                    'email_verified_at' => now(),
                    'account_status' => 'active',
                    'email_verified' => true,
                ]);
            }

            DB::commit();

            Log::info('Business profile verified', [
                'business_profile_id' => $businessProfile->id,
                'vendor_id' => $vendor->id,
                'verified_by' => auth()->id(),
            ]);

            // Send verification email
            Mail::to($businessProfile->user->email)->send(
                new BusinessProfileVerificationMail(
                    $businessProfile->user->name,
                    $businessProfile->business_name
                )
            );

            return redirect()
                ->route('admin.business-profile.index')
                ->with('success', 'Business profile verified successfully! Vendor account created.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to verify business profile', [
                'business_profile_id' => $businessProfile->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Failed to verify business profile: ' . $e->getMessage()]);
        }
    }


    public function print()
{
    $businessProfiles = BusinessProfile::with(['user', 'country'])
        ->get();

    // Calculate statistics
    $total = BusinessProfile::count();
    $pending = BusinessProfile::where('is_admin_verified', false)
        ->where('verification_status', 'pending')
        ->count();
    $verified = BusinessProfile::where('is_admin_verified', true)
        ->where('verification_status', 'verified')
        ->count();
    $rejected = BusinessProfile::where('verification_status', 'rejected')
        ->count();

    $today = BusinessProfile::whereDate('created_at', today())->count();
    $thisWeek = BusinessProfile::whereBetween('created_at', [
        now()->startOfWeek(),
        now()->endOfWeek()
    ])->count();
    $thisMonth = BusinessProfile::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    $stats = [
        'total' => $total,
        'pending' => $pending,
        'verified' => $verified,
        'rejected' => $rejected,
        'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 1) : 0,
        'verified_percentage' => $total > 0 ? round(($verified / $total) * 100, 1) : 0,
        'today' => $today,
        'this_week' => $thisWeek,
        'this_month' => $thisMonth,
    ];

    return view('admin.business-profile.print', compact('businessProfiles', 'stats'));
}

public function switchToVendor(BusinessProfile $businessProfile)
{
    try {
        // Get the vendor for this business profile
        $vendor = Vendor::where('business_profile_id', $businessProfile->id)->first();

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'No Vendor account found for this business profile.'
            ], 404);
        }

        $vendorRole = Role::where('slug', 'vendor')->first();

        if (!$vendorRole) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor role not found in system.'
            ], 404);
        }

        // Get user
        $user = $vendor->user;

        // Attach role if not exists
        if (!$user->roles()->where('role_id', $vendorRole->id)->exists()) {
            $user->roles()->attach($vendorRole->id);
        }

        // Generate login token
        $token = \Illuminate\Support\Str::random(60);
        \Illuminate\Support\Facades\Cache::put(
            'vendor_login_token_' . $token,
            $user->id,
            now()->addMinutes(5)
        );

        return response()->json([
            'success' => true,
            'message' => 'Ready to switch to Vendor Dashboard',
            'login_url' => route('auth.vendor.token-login', ['token' => $token])
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to switch: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Reject the business profile.
     */
    public function reject(Request $request, BusinessProfile $businessProfile)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        try {
            $businessProfile->update([
                'verification_status' => 'rejected',
                'is_admin_verified' => false,
            ]);

            Log::info('Business profile rejected', [
                'business_profile_id' => $businessProfile->id,
                'rejection_reason' => $validated['rejection_reason'] ?? null,
                'rejected_by' => auth()->id(),
            ]);

            // Send rejection email
            Mail::to($businessProfile->user->email)->send(
                new BusinessProfileRejectionMail(
                    $businessProfile->user->name,
                    $businessProfile->business_name,
                    $validated['rejection_reason'] ?? null
                )
            );

            return redirect()
                ->route('admin.business-profile.index')
                ->with('success', 'Business profile rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to reject business profile', [
                'business_profile_id' => $businessProfile->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Failed to reject business profile: ' . $e->getMessage()]);
        }
    }
}
