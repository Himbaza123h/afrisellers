<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Vendor\Vendor;
use App\Models\BusinessProfile;
use App\Models\OwnerID;
use App\Models\Country;
use App\Mail\VendorVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Str;

class VendorController extends Controller
{
    // ==================== STEP 1: ACCOUNT INFORMATION ====================

    public function showStep1()
    {
        return view('vendor.auth.registration-step1');
    }

    public function processStep1(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
        ]);

        // Store in session
        session(['vendor_registration_step1' => $validated]);

        // Redirect to step 2 with data in URL
        return redirect()->route('vendor.register.step2', [
            'email' => $validated['email'],
            'name' => $validated['name']
        ]);
    }

    // ==================== STEP 2: BUSINESS INFORMATION ====================

    public function showStep2(Request $request)
    {
        // Check if step 1 is completed
        if (!session()->has('vendor_registration_step1')) {
            return redirect()->route('vendor.register.step1')
                ->with('error', 'Please complete Step 1 first.');
        }

        $step1Data = session('vendor_registration_step1');
        return view('vendor.auth.registration-step2', compact('step1Data'));
    }

    public function processStep2(Request $request)
    {
        // Check if step 1 is completed
        if (!session()->has('vendor_registration_step1')) {
            return redirect()->route('vendor.register.step1')
                ->with('error', 'Please complete Step 1 first.');
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_registration_number' => 'required|string|max:100|unique:business_profiles',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
        ]);

        // Store in session
        session(['vendor_registration_step2' => $validated]);

        // Redirect to step 3
        return redirect()->route('vendor.register.step3', [
            'business' => $validated['business_name']
        ]);
    }

    // ==================== STEP 3: DOCUMENTS ====================

    public function showStep3(Request $request)
    {
        // Check if previous steps are completed
        if (!session()->has('vendor_registration_step1') || !session()->has('vendor_registration_step2')) {
            return redirect()->route('vendor.register.step1')
                ->with('error', 'Please complete all previous steps.');
        }

        $step1Data = session('vendor_registration_step1');
        $step2Data = session('vendor_registration_step2');

        return view('vendor.auth.registration-step3', compact('step1Data', 'step2Data'));
    }

    public function processStep3(Request $request)
    {
        // Check if previous steps are completed
        if (!session()->has('vendor_registration_step1') || !session()->has('vendor_registration_step2')) {
            return redirect()->route('vendor.register.step1')
                ->with('error', 'Please complete all previous steps.');
        }

        $validated = $request->validate([
            'owner_full_name' => 'required|string|max:255',
            'business_registration_doc' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'owner_id_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Get data from sessions
            $step1Data = session('vendor_registration_step1');
            $step2Data = session('vendor_registration_step2');

            // Create User
            $user = User::create([
                'name' => $step1Data['name'],
                'email' => $step1Data['email'],
                'password' => Hash::make($step1Data['password']),
            ]);

            // Upload Documents
            $businessRegDoc = $request->file('business_registration_doc')->store('vendor/documents', 'public');
            $ownerIdDoc = $request->file('owner_id_document')->store('vendor/documents', 'public');

            // Get country_id from country name
            $country = Country::where('name', $step2Data['country'])->first();
            if (!$country) {
                throw new \Exception('Country not found: ' . $step2Data['country']);
            }

            // Create BusinessProfile (Vendor will be created when admin verifies)
            $businessProfile = BusinessProfile::create([
                'user_id' => $user->id,
                'vendor_id' => null, // Will be set when admin verifies
                'country_id' => $country->id,
                'business_name' => $step2Data['business_name'],
                'business_registration_number' => $step2Data['business_registration_number'],
                'phone' => $step1Data['phone'],
                'phone_code' => '+250', // Default, can be updated later
                'city' => $step2Data['city'],
                'business_registration_doc' => $businessRegDoc,
                'verification_status' => 'pending',
                'is_admin_verified' => false,
            ]);

            // Create OwnerID document
            $ownerID = OwnerID::create([
                'user_id' => $user->id,
                'id_document_path' => $ownerIdDoc,
                'business_document_path' => $businessRegDoc,
            ]);

            // Note: Vendor will be created and vendor role will be assigned when admin verifies the business profile

            DB::commit();

            // Clear session data
            session()->forget(['vendor_registration_step1', 'vendor_registration_step2']);

            // Redirect to success page
            return redirect()->route('vendor.verification.pending')
                ->with('success', 'Registration successful! Your application has been submitted for review. Our team will review it and notify you once approved.')
                ->with('email', $user->email);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // ==================== EMAIL VERIFICATION ====================

    public function verifyEmail($token)
    {
        $vendor = Vendor::where('email_verification_token', $token)
            ->where('email_verified', false)
            ->first();

        if (!$vendor) {
            return redirect()->route('vendor.register.step1')
                ->with('error', 'Invalid or expired verification link.');
        }

        // Mark as verified
        $vendor->update([
            'email_verified' => true,
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'account_status' => 'active', // Activate account
        ]);

        // Login the user
        auth()->loginUsingId($vendor->user_id);

        return redirect()->route('.home')
        // return view('')
            ->with('success', 'Email verified successfully! Your vendor application has been submitted for review.');
    }

public function verificationPending()
{
    $email = session('email');

    // Check if user is already logged in with active vendor account
    if (auth()->check()) {
        $user = auth()->user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        if ($vendor && $vendor->email_verified && $vendor->account_status === 'active') {
            Log::info('User already verified - Redirecting to Dashboard', [
                'user_id' => $user->id,
                'vendor_id' => $vendor->id,
            ]);

            return redirect()->route('vendor.dashboard.home')
                ->with('success', 'Your account is already verified!');
        }
    }

    // Check if email in session belongs to an already verified vendor (not logged in)
    if ($email) {
        $user = User::where('email', $email)->first();

        if ($user) {
            $vendor = Vendor::where('user_id', $user->id)->first();

            if ($vendor && $vendor->email_verified && $vendor->account_status === 'active') {
                Log::info('Email belongs to verified vendor - Redirecting to Login', [
                    'email' => $email,
                    'vendor_id' => $vendor->id,
                ]);

                // Clear the session email since account is already verified
                session()->forget('email');

                return redirect()->route('auth.signin')
                    ->with('success', 'Your account is already verified! Please login to continue.');
            }
        }
    }

    return view('vendor.auth.verification-pending', compact('email'));
}

// ==================== OTP VERIFICATION ====================

public function verifyOtp(Request $request)
{
    Log::info('=== OTP VERIFICATION ATTEMPT ===');
    Log::info('Request Data:', $request->all());

    $validated = $request->validate([
        'otp' => 'required|string|size:6|regex:/^[0-9]{6}$/',
    ], [
        'otp.required' => 'Please enter the verification code.',
        'otp.size' => 'Verification code must be exactly 6 digits.',
        'otp.regex' => 'Verification code must contain only numbers.',
    ]);

    Log::info('Validated OTP:', ['otp' => $validated['otp']]);

    // DEBUG: Check all unverified vendors
    $allUnverifiedVendors = Vendor::where('email_verified', false)->get();
    Log::info('All Unverified Vendors:', [
        'count' => $allUnverifiedVendors->count(),
        'vendors' => $allUnverifiedVendors->map(function($v) {
            return [
                'id' => $v->id,
                'user_id' => $v->user_id,
                'business_name' => $v->business_name,
                'email_verification_token' => $v->email_verification_token,
                'email_verified' => $v->email_verified,
            ];
        })->toArray()
    ]);

    // Find vendor by OTP token
    $vendor = Vendor::where('email_verification_token', $validated['otp'])
        ->where('email_verified', false)
        ->first();

    if (!$vendor) {
        Log::warning('OTP Verification Failed - Invalid Token', [
            'provided_otp' => $validated['otp'],
            'ip_address' => $request->ip(),
        ]);

        // DEBUG: Try to find vendor with any token (case sensitivity check)
        $vendorAnyCase = Vendor::whereRaw('LOWER(email_verification_token) = ?', [strtolower($validated['otp'])])
            ->where('email_verified', false)
            ->first();

        Log::info('Case Insensitive Search Result:', [
            'found' => $vendorAnyCase ? 'yes' : 'no',
            'vendor_id' => $vendorAnyCase->id ?? null,
            'stored_token' => $vendorAnyCase->email_verification_token ?? null,
        ]);

        return back()->with('error', 'Invalid verification code. Please check and try again.');
    }

    Log::info('Vendor Found for OTP Verification', [
        'vendor_id' => $vendor->id,
        'user_id' => $vendor->user_id,
        'business_name' => $vendor->business_name,
        'email' => $vendor->user->email ?? 'N/A',
    ]);

    // Mark as verified
    $vendor->update([
        'email_verified' => true,
        'email_verified_at' => now(),
        'email_verification_token' => null,
        'account_status' => 'active',
    ]);

    Log::info('Vendor Email Verified Successfully', [
        'vendor_id' => $vendor->id,
        'verified_at' => $vendor->email_verified_at,
        'account_status' => $vendor->account_status,
    ]);

    // Login the user
    auth()->loginUsingId($vendor->user_id);

    Log::info('User Logged In After Verification', [
        'user_id' => $vendor->user_id,
        'auth_user' => auth()->user()->email ?? 'N/A',
    ]);

    Log::info('=== OTP VERIFICATION SUCCESSFUL ===');

    return redirect()->route('vendor.dashboard.home')
        ->with('success', 'Email verified successfully! Your vendor application has been submitted for review.');
}

// ==================== RESEND VERIFICATION EMAIL ====================

public function resendVerification(Request $request)
{
    Log::info('=== RESEND VERIFICATION EMAIL ATTEMPT ===');
    Log::info('Session Data:', [
        'email' => session('email'),
        'ip_address' => $request->ip(),
    ]);

    // Get email from session (from registration)
    $email = session('email');

    if (!$email) {
        Log::error('Resend Failed - No Email in Session');
        return back()->with('error', 'Unable to resend verification email. Please register again.');
    }

    // Find user and vendor
    $user = User::where('email', $email)->first();

    if (!$user) {
        Log::error('Resend Failed - User Not Found', ['email' => $email]);
        return back()->with('error', 'User not found. Please register again.');
    }

    Log::info('User Found for Resend', [
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
    ]);

    $vendor = Vendor::where('user_id', $user->id)
        ->where('email_verified', false)
        ->first();

    if (!$vendor) {
        Log::warning('Resend Failed - Vendor Not Found or Already Verified', [
            'user_id' => $user->id,
            'email' => $email,
        ]);
        return back()->with('error', 'Vendor account not found or already verified.');
    }

    Log::info('Vendor Found - Generating New Token', [
        'vendor_id' => $vendor->id,
        'old_token' => $vendor->email_verification_token,
    ]);

    // Generate new verification token
    $verificationToken = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    Log::info('New Verification Token Generated', [
        'vendor_id' => $vendor->id,
        'new_token' => $verificationToken,
    ]);

    // Update vendor with new token
    $vendor->update([
        'email_verification_token' => $verificationToken,
    ]);

    Log::info('Vendor Updated with New Token', [
        'vendor_id' => $vendor->id,
        'token_updated' => true,
    ]);

    try {
        // Resend verification email
        Mail::to($user->email)->send(new VendorVerificationMail($user->name, $verificationToken));

        Log::info('Verification Email Sent Successfully', [
            'to' => $user->email,
            'vendor_id' => $vendor->id,
            'token' => $verificationToken,
        ]);

        Log::info('=== RESEND VERIFICATION EMAIL SUCCESSFUL ===');

        return back()->with('success', 'Verification email has been resent successfully! Please check your inbox.');
    } catch (\Exception $e) {
        Log::error('Failed to Send Verification Email', [
            'vendor_id' => $vendor->id,
            'email' => $user->email,
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
        ]);

        return back()->with('error', 'Failed to send verification email. Please try again later.');
    }
}

}
