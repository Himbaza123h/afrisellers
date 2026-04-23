<?php

namespace App\Http\Controllers\Admin\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Vendor;
use App\Models\User;
use App\Models\Role;
use App\Models\BusinessProfile;
use App\Models\OwnerID;
use App\Models\Country;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    /**
     * Legacy list URL: vendors are managed via Business profiles.
     */
    public function index()
    {
        return redirect()->route('admin.business-profile.index');
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['user.products', 'businessProfile.country', 'ownerID']);

        $stats = [
            'total_products' => $vendor->user?->products()->count() ?? 0,
            'active_products' => $vendor->user?->products()->where('status', 'active')->count() ?? 0,
            'pending_products' => $vendor->user?->products()->where('status', 'pending')->count() ?? 0,
        ];

        return view('admin.vendors.show', compact('vendor', 'stats'));
    }

    public function verify(Vendor $vendor)
    {
        if ($vendor->businessProfile) {
            $vendor->businessProfile->update([
                'is_admin_verified' => true,
                'verification_status' => 'verified',
            ]);
        }

        return redirect()->back()->with('success', 'Vendor business profile verified successfully.');
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $plans     = Plan::orderBy('name')->get();

        return view('admin.vendors.create', compact('countries', 'plans'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'name'                         => 'required|string|max:255',
            'email'                        => 'required|email|unique:users,email',
            'password'                     => 'required|string|min:8|confirmed',
            'phone'                        => 'required|string|max:20',
            'phone_code'                   => 'nullable|string|max:10',
            'business_name'                => 'required|string|max:255',
            'business_registration_number' => 'required|string|max:100|unique:business_profiles',
            'country_id'                   => 'required|exists:countries,id',
            'city'                         => 'required|string|max:100',
            'description'                  => 'nullable|string',
            'business_registration_doc'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'owner_id_document'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'owner_full_name'              => 'nullable|string|max:255',
            'plan_id'                      => 'nullable|exists:plans,id',
            'account_status'               => 'required|in:pending,active,verified,suspended',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create User
            $user = User::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'country_id' => $request->country_id,
            ]);

            // 2. Assign vendor role
            $vendorRole = Role::where('slug', 'vendor')->first();
            if ($vendorRole) {
                $user->roles()->syncWithoutDetaching($vendorRole);
            }

            // 3. Upload documents
            $businessRegDoc = $request->hasFile('business_registration_doc')
                ? $request->file('business_registration_doc')->store('vendor/documents', 'public')
                : null;

            $ownerIdDoc = $request->hasFile('owner_id_document')
                ? $request->file('owner_id_document')->store('vendor/documents', 'public')
                : null;

            // 4. Create BusinessProfile
            $businessProfile = BusinessProfile::create([
                'user_id'                      => $user->id,
                'vendor_id'                    => null,
                'country_id'                   => $request->country_id,
                'business_name'                => $request->business_name,
                'business_registration_number' => $request->business_registration_number,
                'phone'                        => $request->phone,
                'phone_code'                   => $request->phone_code ?? '+250',
                'city'                         => $request->city,
                'description'                  => $request->description,
                'business_registration_doc'    => $businessRegDoc,
                'verification_status'          => in_array($request->account_status, ['verified', 'active']) ? 'verified' : 'pending',
                'is_admin_verified'            => in_array($request->account_status, ['verified', 'active']),
            ]);

            // 5. Create OwnerID (if doc uploaded)
            $ownerID = null;
            if ($ownerIdDoc) {
                $ownerID = OwnerID::create([
                    'user_id'                => $user->id,
                    'owner_full_name'        => $request->owner_full_name,
                    'id_document_path'       => $ownerIdDoc,
                    'business_document_path' => $businessRegDoc,
                ]);
            }

            // 6. Create Vendor
            $vendor = Vendor::create([
                'user_id'                  => $user->id,
                'business_profile_id'      => $businessProfile->id,
                'owner_id_document_id'     => $ownerID?->id,
                'plan_id'                  => $request->plan_id,
                'account_status'           => $request->account_status,
                'email_verified'           => true,
                'email_verified_at'        => now(),
                'email_verification_token' => null,
            ]);

            // 7. Link vendor_id back onto BusinessProfile
            $businessProfile->update(['vendor_id' => $vendor->id]);

            DB::commit();

            // Notify the new vendor
            \App\Models\Notification::create([
                'title'     => 'Welcome to AfriSellers! 🎉',
                'content'   => 'Your vendor account for ' . $request->business_name . ' has been created by the admin. You can now log in and start listing your products.',
                'link_url'  => '/vendor/dashboard',
                'user_id'   => $user->id,
                'vendor_id' => $vendor->id,
                'country_id'=> $request->country_id,
                'is_read'   => false,
            ]);

            Log::info('Admin created vendor', [
                'vendor_id' => $vendor->id,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('admin.business-profile.index')
                ->with('success', "Vendor '{$request->business_name}' created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin Vendor Create Failed: ' . $e->getMessage());

            return back()->withInput()
                ->withErrors(['error' => 'Failed to create vendor: ' . $e->getMessage()]);
        }
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function edit(Vendor $vendor)
    {
        $vendor->load(['user', 'businessProfile.country', 'ownerID']);
        $countries = Country::orderBy('name')->get();
        $plans     = Plan::orderBy('name')->get();

        return view('admin.vendors.edit', compact('vendor', 'countries', 'plans'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name'                         => 'required|string|max:255',
            'email'                        => 'required|email|unique:users,email,' . $vendor->user_id,
            'phone'                        => 'required|string|max:20',
            'phone_code'                   => 'nullable|string|max:10',
            'business_name'                => 'required|string|max:255',
            'business_registration_number' => 'required|string|max:100|unique:business_profiles,business_registration_number,' . $vendor->businessProfile?->id,
            'country_id'                   => 'required|exists:countries,id',
            'city'                         => 'required|string|max:100',
            'description'                  => 'nullable|string',
            'business_registration_doc'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'owner_id_document'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'owner_full_name'              => 'nullable|string|max:255',
            'plan_id'                      => 'nullable|exists:plans,id',
            'account_status'               => 'required|in:pending,active,verified,suspended',
            'password'                     => 'nullable|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            // Update User
            $userUpdate = [
                'name'       => $request->name,
                'email'      => $request->email,
                'country_id' => $request->country_id,
            ];
            if ($request->filled('password')) {
                $userUpdate['password'] = Hash::make($request->password);
            }
            $vendor->user->update($userUpdate);

            // Handle business registration doc upload
            $businessProfile  = $vendor->businessProfile;
            $businessRegDoc   = $businessProfile->business_registration_doc;

            if ($request->hasFile('business_registration_doc')) {
                if ($businessRegDoc) {
                    Storage::disk('public')->delete($businessRegDoc);
                }
                $businessRegDoc = $request->file('business_registration_doc')
                    ->store('vendor/documents', 'public');
            }

            // Update BusinessProfile
            $businessProfile->update([
                'country_id'                   => $request->country_id,
                'business_name'                => $request->business_name,
                'business_registration_number' => $request->business_registration_number,
                'phone'                        => $request->phone,
                'phone_code'                   => $request->phone_code ?? $businessProfile->phone_code,
                'city'                         => $request->city,
                'description'                  => $request->description,
                'business_registration_doc'    => $businessRegDoc,
                'verification_status'          => $request->account_status === 'verified' ? 'verified' : $businessProfile->verification_status,
                'is_admin_verified'            => in_array($request->account_status, ['verified', 'active']),
            ]);

            // Handle owner ID doc upload
            if ($request->hasFile('owner_id_document')) {
                $ownerIdDoc = $request->file('owner_id_document')
                    ->store('vendor/documents', 'public');

                if ($vendor->ownerID) {
                    if ($vendor->ownerID->id_document_path) {
                        Storage::disk('public')->delete($vendor->ownerID->id_document_path);
                    }
                    $vendor->ownerID->update([
                        'id_document_path' => $ownerIdDoc,
                        'owner_full_name'  => $request->owner_full_name ?? $vendor->ownerID->owner_full_name,
                    ]);
                } else {
                    $ownerID = OwnerID::create([
                        'user_id'          => $vendor->user_id,
                        'owner_full_name'  => $request->owner_full_name,
                        'id_document_path' => $ownerIdDoc,
                    ]);
                    $vendor->update(['owner_id_document_id' => $ownerID->id]);
                }
            } elseif ($request->filled('owner_full_name') && $vendor->ownerID) {
                $vendor->ownerID->update(['owner_full_name' => $request->owner_full_name]);
            }

            // Update Vendor
            $vendor->update([
                'plan_id'        => $request->plan_id,
                'account_status' => $request->account_status,
            ]);

            DB::commit();

            // Notify the vendor of profile update
            \App\Models\Notification::create([
                'title'     => 'Your Vendor Profile Was Updated',
                'content'   => 'Your vendor account for ' . $request->business_name . ' has been updated by the admin. Please review your profile.',
                'link_url'  => '/vendor/profile',
                'user_id'   => $vendor->user_id,
                'vendor_id' => $vendor->id,
                'country_id'=> $request->country_id,
                'is_read'   => false,
            ]);

            // Notify if account status changed
            $statusMessages = [
                'active'    => ['title' => 'Account Activated',  'content' => 'Your vendor account has been activated. You can now sell on AfriSellers.'],
                'suspended' => ['title' => 'Account Suspended',  'content' => 'Your vendor account has been suspended. Please contact the admin for assistance.'],
                'pending'   => ['title' => 'Account Pending',    'content' => 'Your vendor account has been set to pending review.'],
                'verified'  => ['title' => 'Account Verified ✅', 'content' => 'Your vendor account has been fully verified. Enjoy all AfriSellers features.'],
            ];

            if (isset($statusMessages[$request->account_status])) {
                $msg = $statusMessages[$request->account_status];
                \App\Models\Notification::create([
                    'title'     => $msg['title'],
                    'content'   => $msg['content'],
                    'link_url'  => '/vendor/dashboard',
                    'user_id'   => $vendor->user_id,
                    'vendor_id' => $vendor->id,
                    'country_id'=> $request->country_id,
                    'is_read'   => false,
                ]);
            }

            Log::info('Admin updated vendor', [
                'vendor_id'  => $vendor->id,
                'updated_by' => auth()->id(),
            ]);

            return redirect()->route('admin.business-profile.show', $businessProfile)
                ->with('success', 'Vendor updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin Vendor Update Failed: ' . $e->getMessage());

            return back()->withInput()
                ->withErrors(['error' => 'Failed to update vendor: ' . $e->getMessage()]);
        }
    }
}
