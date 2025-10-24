<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function showRegistrationForm()
    {
        return view('vendor.auth.registration');
    }

    public function register(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'business_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'business_registration_number' => 'required|string|max:100|unique:vendors',
            'business_registration_doc' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'owner_id_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'owner_full_name' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Create User
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Upload Documents
            $businessRegDoc = $request->file('business_registration_doc')->store('vendor/documents', 'public');
            $ownerIdDoc = $request->file('owner_id_document')->store('vendor/documents', 'public');

            // Create Vendor
            $vendor = Vendor::create([
                'user_id' => $user->id,
                'business_name' => $validated['business_name'],
                'phone' => $validated['phone'],
                'country' => $validated['country'],
                'city' => $validated['city'],
                'business_registration_number' => $validated['business_registration_number'],
                'business_registration_doc' => $businessRegDoc,
                'owner_id_document' => $ownerIdDoc,
                'owner_full_name' => $validated['owner_full_name'],
                'verification_status' => 'pending',
                'account_status' => 'active',
            ]);

            // Assign Vendor Role
            $vendorRole = Role::where('slug', 'vendor')->first();
            if ($vendorRole) {
                $user->assignRole($vendorRole);
            }

            DB::commit();

            // Login the user
            auth()->login($user);

            return redirect()->route('vendor.dashboard')->with('success', 'Registration successful! Your account is under review.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }
}
