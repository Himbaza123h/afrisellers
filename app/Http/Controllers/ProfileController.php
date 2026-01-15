<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Show the user's profile.
     */
    public function show()
    {
        try {
            $user = Auth::user();

        if ($user->hasRole('admin')) {
            // Admin profile
            return view('profile.show', compact('user'));
        } elseif ($user->isVendor()) {
            $user->load(['vendor.businessProfile', 'vendor.ownerID']);
            return view('profile.show', compact('user'));
        } else {
            // Buyer profile
            $user->load('buyer');
            return view('profile.show', compact('user'));
        }
        } catch (\Exception $e) {
            Log::error('Profile Show Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while loading your profile.');
        }
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            // Buyer-specific fields
            'phone' => 'nullable|string|max:20',
            'phone_code' => 'nullable|string|max:10',
            'country_id' => 'nullable|exists:countries,id',
            'city' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
            'sex' => 'nullable|in:Male,Female',
            'business_name' => 'nullable|string|max:255',
            'business_email' => 'nullable|email|max:255',
            'business_phone' => 'nullable|string|max:20',
            'business_address' => 'nullable|string|max:500',
        ]);

        try {
            // Update user basic info
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // Update buyer profile if user is a buyer
            if ($user->buyer) {
                $user->buyer->update([
                    'phone' => $validated['phone'] ?? null,
                    'phone_code' => $validated['phone_code'] ?? null,
                    'country_id' => $validated['country_id'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'sex' => $validated['sex'] ?? null,
                ]);
            }

            // Update vendor business profile if user is a vendor
            if ($user->vendor && $user->vendor->businessProfile) {
                $user->vendor->businessProfile->update([
                    'business_name' => $validated['business_name'] ?? $user->vendor->businessProfile->business_name,
                    'business_email' => $validated['business_email'] ?? $user->vendor->businessProfile->business_email,
                    'phone' => $validated['business_phone'] ?? $user->vendor->businessProfile->phone,
                    'address' => $validated['business_address'] ?? $user->vendor->businessProfile->address,
                ]);
            }

            Log::info('Profile updated', [
                'user_id' => $user->id,
            ]);

            return redirect()
                ->route('profile.show')
                ->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update profile', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Failed to update profile. Please try again.']);
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = Auth::user();

            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'The current password is incorrect.']);
            }

            // Update password
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            Log::info('Password updated', [
                'user_id' => $user->id,
            ]);

            return redirect()
                ->route('profile.show')
                ->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update password', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Failed to update password. Please try again.']);
        }
    }
}
