<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Buyer\Buyer;
use App\Models\Vendor\Vendor;
use App\Models\BusinessProfile;
use App\Models\OwnerID;
use App\Models\Country;
use App\Mail\BuyerVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BuyerController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'phone_code' => 'required|string|max:10',
            'country_id' => 'required|exists:countries,id',
            'city' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'sex' => 'required|in:Male,Female,Other',
            'password' => 'required|string|min:8|confirmed',
            'agree_terms' => 'required|accepted',
        ]);

        try {
            DB::beginTransaction();

            // Create User
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Generate verification token (6-digit code)
            $verificationToken = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Create Buyer
            $buyer = Buyer::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'],
                'phone_code' => $validated['phone_code'],
                'country_id' => $validated['country_id'],
                'city' => $validated['city'],
                'date_of_birth' => $validated['date_of_birth'],
                'sex' => $validated['sex'],
                'account_status' => 'pending',
                'email_verification_token' => $verificationToken,
                'email_verified' => false,
            ]);

            // Assign Buyer Role
            $buyerRole = Role::where('slug', 'buyer')->firstOrCreate(
                [
                    'slug' => 'buyer',
                ],
                [
                    'name' => 'Buyer',
                    'description' => 'Buyer role for customers',
                ],
            );

            $user->assignRole($buyerRole);

            // Send verification email
            Mail::to($user->email)->send(new BuyerVerificationMail($user->name, $verificationToken));

            DB::commit();

            // Redirect to verification page
            return redirect()->route('buyer.verification.show')->with('success', 'Registration successful! Please check your email for verification code.')->with('email', $user->email)->with('user_id', $user->id);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function showVerification()
    {
        $email = session('email');
        $userId = session('user_id');

        if (!$email || !$userId) {
            return redirect()->route('auth.register')->with('error', 'Please register first.');
        }

        return view('frontend.auth.buyer-verification', compact('email'));
    }

    public function verifyEmail(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|size:6',
        ]);

        $buyer = Buyer::where('email_verification_token', $validated['token'])->where('email_verified', false)->first();

        if (!$buyer) {
            return back()->withErrors(['token' => 'Invalid or expired verification code.']);
        }

        // Mark as verified and activate account
        $buyer->update([
            'email_verified' => true,
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'account_status' => 'active',
        ]);

        // Login the user
        auth()->loginUsingId($buyer->user_id);

        // Clear session data
        session()->forget(['email', 'user_id']);

        return redirect()->route('home')->with('success', 'Email verified successfully! Your account is now active.');
    }

    public function resendVerification(Request $request)
    {
        $email = session('email');

        if (!$email) {
            return redirect()->route('auth.register')->with('error', 'Please register first.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('auth.register')->with('error', 'User not found.');
        }

        $buyer = $user->buyer;

        if ($buyer->email_verified) {
            return redirect()->route('home')->with('info', 'Your email is already verified.');
        }

        // Generate new verification token
        $verificationToken = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $buyer->update([
            'email_verification_token' => $verificationToken,
        ]);

        // Resend verification email
        Mail::to($user->email)->send(new BuyerVerificationMail($user->name, $verificationToken));

        return back()->with('success', 'Verification code has been resent to your email.');
    }

    /**
     * Show become vendor form
     */
    public function showBecomeVendor()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('auth.signin')->with('error', 'Please login to become a vendor.');
        }

        // Check if user already has a vendor account
        $user = Auth::user();
        $existingVendor = Vendor::where('user_id', $user->id)->first();

        if ($existingVendor) {
            return redirect()->route('vendor.dashboard.home')->with('info', 'You already have a vendor account.');
        }

        // Get active countries from database
        $countries = Country::where('status', 'active')->orderBy('name')->get();

        return view('buyer.become-vendor', compact('countries'));
    }

    /**
     * Store become vendor step 1 (Business Information)
     */
    public function storeBecomeVendor(Request $request)
    {
        $user = Auth::user();

        // Check if user already has a vendor account
        $existingVendor = Vendor::where('user_id', $user->id)->first();

        if ($existingVendor) {
            return redirect()->route('vendor.dashboard.home')->with('info', 'You already have a vendor account.');
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_registration_number' => 'required|string|max:100|unique:business_profiles',
            'phone' => 'required|string|max:20',
            'phone_code' => 'required|string|max:10',
            'country_id' => 'required|exists:countries,id',
            'city' => 'required|string|max:100',
        ]);

        try {
            DB::beginTransaction();
            // Create business profile (Vendor will be created when admin verifies)
            $businessProfile = BusinessProfile::create([
                'user_id' => $user->id,
                'country_id' => $validated['country_id'],
                'business_name' => $validated['business_name'],
                'business_registration_number' => $validated['business_registration_number'],
                'phone' => $validated['phone'],
                'phone_code' => $validated['phone_code'] ?? '+250',
                'city' => $validated['city'],
                'verification_status' => 'pending',
                'is_admin_verified' => false,
            ]);

            DB::commit();
            return redirect()->route('buyer.become-vendor.step2', ['id' => $businessProfile->id]);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Failed to save business information: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show become vendor step 2 (Documents)
     */
    public function showBecomeVendorStep2($id)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('auth.signin')->with('error', 'Please login to become a vendor.');
        }

        $user = Auth::user();

        // Check if user already has a vendor account
        $existingVendor = Vendor::where('user_id', $user->id)->first();

        if ($existingVendor) {
            return redirect()->route('vendor.dashboard.home')->with('info', 'You already have a vendor account.');
        }

        // Load business profile from database - check user_id directly since vendor doesn't exist yet
        $businessProfile = BusinessProfile::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        // Prepare step1Data for view compatibility
        $step1Data = [
            'business_name' => $businessProfile->business_name,
            'business_registration_number' => $businessProfile->business_registration_number,
            'city' => $businessProfile->city,
            'country_id' => $businessProfile->country_id,
        ];

        return view('buyer.become-vendor-step-2', compact('step1Data', 'businessProfile'));
    }

    /**
     * Store become vendor step 2 (Documents) and update business profile
     */
    public function storeBecomeVendorStep2(Request $request, $id)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('auth.signin')->with('error', 'Please login to become a vendor.');
        }

        $user = Auth::user();

        // Check if user already has a vendor account
        $existingVendor = Vendor::where('user_id', $user->id)->first();

        if ($existingVendor) {
            return redirect()->route('vendor.dashboard.home')->with('info', 'You already have a vendor account.');
        }

        $validated = $request->validate([
            'id_number' => 'required|string|max:100',
            'business_registration_doc' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'owner_id_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Upload documents
            $businessRegDoc = $request->file('business_registration_doc')->store('vendor/documents', 'public');
            $ownerIdDoc = $request->file('owner_id_document')->store('vendor/documents', 'public');

            // Create owner ID document (linked to user, vendor will be created when admin verifies)
            $ownerID = OwnerID::create([
                'user_id' => $user->id,
                'id_number' => $validated['id_number'],
                'id_document_path' => $ownerIdDoc,
                'business_document_path' => $businessRegDoc,
            ]);

            // Note: Vendor role will be assigned when admin verifies the business profile

            DB::commit();

            return redirect()->route('buyer.dashboard.home')->with('success', 'Your vendor application has been submitted successfully! Our team will review it and notify you once approved.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Failed to submit application: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show submitted business profile
     */
    public function showSubmittedBusiness()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('auth.signin')->with('error', 'Please login to view your business profile.');
        }

        $user = Auth::user();

        // Get business profile for this user
        $businessProfile = BusinessProfile::where('user_id', $user->id)
            ->with(['country', 'user'])
            ->first();

        if (!$businessProfile) {
            return redirect()->route('buyer.dashboard.home')->with('error', 'No business profile found. Please submit a vendor application first.');
        }

        // Get owner ID information
        $ownerID = OwnerID::where('user_id', $user->id)->first();

        return view('buyer.submitted-business', compact('businessProfile', 'ownerID'));
    }
}
