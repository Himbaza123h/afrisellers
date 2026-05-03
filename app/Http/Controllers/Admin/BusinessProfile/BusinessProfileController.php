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
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\VendorTrial;
use App\Models\AgentCredit;
use App\Models\Credit;
use App\Models\CreditTransaction;

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
    $latestRejected = BusinessProfile::where('verification_status', 'rejected')
        ->whereNotNull('rejection_reason')
        ->latest('updated_at')
        ->first();

    return view('admin.business-profile.index', compact(
        'businessProfiles',
        'countries',
        'stats',
        'latestRejected'
    ));
}

    /**
     * Show the specified business profile.
     */
    public function show(Request $request, BusinessProfile $businessProfile)
    {
        $businessProfile->load(['user', 'country']);

        // Owner ID document
        $ownerID = OwnerID::where('user_id', $businessProfile->user_id)->first();

        // Vendor record (may or may not exist yet)
        $vendor = Vendor::where('user_id', $businessProfile->user_id)
                        ->orWhere('business_profile_id', $businessProfile->id)
                        ->first();

        // ── Products belonging to this business owner ──────────
        $productsQuery = Product::where('user_id', $businessProfile->user_id)
            ->with(['productCategory', 'images', 'country'])
            ->latest();

        if ($request->filled('prod_search')) {
            $productsQuery->where('name', 'like', '%' . $request->prod_search . '%');
        }
        if ($request->filled('prod_status')) {
            $productsQuery->where('status', $request->prod_status);
        }
        if ($request->filled('prod_category')) {
            $productsQuery->where('product_category_id', $request->prod_category);
        }

        $vendorProducts = $productsQuery->paginate(12, ['*'], 'prod_page');

        $productCategories = ProductCategory::orderBy('name')->get();

        $productStats = [
            'total'    => Product::where('user_id', $businessProfile->user_id)->count(),
            'approved' => Product::where('user_id', $businessProfile->user_id)->where('status', 'active')->count(),
            'pending'  => Product::where('user_id', $businessProfile->user_id)->where('status', 'draft')->count(),
            'rejected' => Product::where('user_id', $businessProfile->user_id)->where('status', 'inactive')->count(),
        ];

        return view('admin.business-profile.show', compact(
            'businessProfile',
            'ownerID',
            'vendor',
            'vendorProducts',
            'productCategories',
            'productStats',
        ));
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

           // Check if vendor already exists (re-verification of rejected vendor)
            $existingVendor = Vendor::where('user_id', $businessProfile->user_id)
                ->orWhere('business_profile_id', $businessProfile->id)
                ->first();

            if ($existingVendor) {
                // Just reactivate — do NOT create a new vendor
                $existingVendor->update([
                    'account_status' => 'active',
                    'email_verified' => true,
                    'email_verified_at' => now(),
                    'plan_id' => $defaultPlan?->id,
                ]);
                $vendor = $existingVendor;

                Log::info('Existing vendor reactivated on re-verification', [
                    'vendor_id' => $vendor->id,
                    'user_id'   => $businessProfile->user_id,
                ]);
            } else {
                // Fresh vendor — create new
                $vendor = $businessProfile->createVendorOnVerification();

                if (!$vendor) {
                    $ownerID = OwnerID::where('user_id', $businessProfile->user_id)->first();
                    $verificationToken = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                    $vendor = Vendor::create([
                        'user_id'                  => $businessProfile->user_id,
                        'business_profile_id'      => $businessProfile->id,
                        'owner_id_document_id'     => $ownerID?->id,
                        'plan_id'                  => $defaultPlan?->id,
                        'email_verification_token' => $verificationToken,
                        'email_verified_at'        => now(),
                        'account_status'           => 'active',
                        'email_verified'           => true,
                    ]);

                    $businessProfile->update(['vendor_id' => $vendor->id]);
                } else {
                    $verificationToken = $vendor->email_verification_token
                        ?? str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                    $vendor->update([
                        'plan_id'                  => $defaultPlan?->id,
                        'email_verification_token' => $verificationToken,
                        'email_verified_at'        => now(),
                        'account_status'           => 'active',
                        'email_verified'           => true,
                    ]);
                }

                // Assign vendor role only for new vendors
                $vendorRole = Role::where('slug', 'vendor')->first();
                if ($vendorRole && !$businessProfile->user->hasRole('vendor')) {
                    $businessProfile->user->assignRole($vendorRole);
                }
            }

            // Assign trial — only if no trial exists yet for this user
            $trialAlreadyExists = VendorTrial::where('user_id', $businessProfile->user_id)->exists();

            if (!$trialAlreadyExists && $defaultPlan) {
                VendorTrial::create([
                    'vendor_id'  => $vendor->id,
                    'user_id'    => $businessProfile->user_id,
                    'plan_id'    => $defaultPlan->id,
                    'starts_at'  => now(),
                    'ends_at'    => now()->addDays($defaultPlan->trial_days ?? 30),
                    'is_active'  => true,
                ]);

                Log::info('Trial membership assigned', [
                    'vendor_id' => $vendor->id,
                    'plan_id'   => $defaultPlan->id,
                    'ends_at'   => now()->addDays($defaultPlan->trial_days ?? 30),
                ]);
            }
            // ── Award credits to agent when vendor is verified ─────────────
            try {
                $agentId = $vendor->agent_id ?? null;
                if ($agentId) {
                    $creditEntry  = Credit::where('type', 'agent_registration')->first();
                    $creditAmount = $creditEntry ? (float) $creditEntry->value : 5.0;

                    $agentCredit = AgentCredit::firstOrNew(['agent_id' => $agentId]);
                    $agentCredit->total_credits = (float) ($agentCredit->total_credits ?? 0) + $creditAmount;
                    $agentCredit->save();

                    CreditTransaction::create([
                        'agent_id'         => $agentId,
                        'transaction_type' => 'vendor_verified_reward',
                        'credits'          => $creditAmount,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Credit reward failed on vendor verification: ' . $e->getMessage());
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
        'rejection_reason' => 'nullable|string',
    ]);

    try {
        $businessProfile->update([
            'verification_status' => 'rejected',
            'is_admin_verified' => false,
            'rejection_reason' => $validated['rejection_reason'] ?? null,
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

    public function replyToRejection(Request $request, BusinessProfile $businessProfile)
{
    $validated = $request->validate([
        'reason_reply' => 'required|string',
    ]);

    $businessProfile->update([
        'reason_reply' => $validated['reason_reply'],
    ]);

    return back()->with('success', 'Reply saved successfully.');
}

public function notifyVendors(Request $request)
{
    $request->validate([
        'subject'     => 'required|string|max:255',
        'message'     => 'required|string',
        'attachment'  => 'nullable|file|max:10240',
        'vendor_id'   => 'nullable|exists:users,id',
    ]);

    $attachmentPath = null;
    if ($request->hasFile('attachment')) {
        $attachmentPath = $request->file('attachment')->store('vendor-notifications', 'public');
    }

    if ($request->filled('vendor_id')) {
        $users = \App\Models\User::where('id', $request->vendor_id)->get();
    } else {
        $users = \App\Models\User::whereHas('vendor')->get();
    }

    foreach ($users as $user) {
        try {
            \Illuminate\Support\Facades\Mail::send([], [], function ($mail) use ($user, $request, $attachmentPath) {
                $mail->to($user->email, $user->name)
                     ->subject($request->subject)
                     ->html('<div style="font-family:sans-serif;max-width:600px;margin:auto;">'
                         . '<h2 style="color:#ff0808;">Message from AfriSellers Admin</h2>'
                         . '<p>Dear ' . e($user->name) . ',</p>'
                         . $request->message
                         . '<br><br><p style="color:#888;font-size:12px;">AfriSellers Team</p></div>');
                if ($attachmentPath) {
                    // $mail->attach(\Illuminate\Support\Facades\Storage::path('public/' . $attachmentPath));
                    $mail->attach(\Illuminate\Support\Facades\Storage::disk('public')->path($attachmentPath));
                }
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Vendor notification email failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    return back()->with('success', 'Notification sent successfully to ' . $users->count() . ' vendor(s).');
}

public function activate(BusinessProfile $businessProfile)
{
    try {
        $businessProfile->update([
            'verification_status' => 'verified',
            'is_admin_verified'   => true,
        ]);

        // Also reactivate vendor account if exists
        $vendor = Vendor::where('user_id', $businessProfile->user_id)->first();
        if ($vendor) {
            $vendor->update(['account_status' => 'active']);
        }

        return back()->with('success', 'Business profile reactivated successfully.');
    } catch (\Exception $e) {
        Log::error('Failed to activate business profile', [
            'business_profile_id' => $businessProfile->id,
            'error' => $e->getMessage(),
        ]);
        return back()->withErrors(['error' => 'Failed to activate: ' . $e->getMessage()]);
    }
}

public function destroyUser(Request $request, BusinessProfile $businessProfile)
{
    $user = $businessProfile->user;

    if (!$user) {
        return back()->with('error', 'User not found.');
    }

    $expected = 'delete-' . $user->email;

    if ($request->input('confirmation') !== $expected) {
        return back()->with('error', 'Confirmation text did not match. User was NOT deleted.');
    }

    DB::transaction(function () use ($businessProfile, $user) {
        // Soft delete vendor if exists
        \App\Models\Vendor\Vendor::where('user_id', $user->id)->delete();

        // Soft delete all business profiles
        BusinessProfile::where('user_id', $user->id)->delete();

        // Soft delete the user
        $user->delete();
    });

    return redirect()->route('admin.business-profile.index')
        ->with('success', 'User and all related data have been deleted successfully.');
}
}
