<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Buyer\Buyer;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    /**
     * Display the account settings page.
     */
    public function index()
    {
        $user = auth()->user();
        $buyer = Buyer::where('user_id', $user->id)->first();
        $countries = Country::where('status', 'active')->orderBy('name')->get();

        return view('buyer.account.settings', compact('user', 'buyer', 'countries'));
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $buyer = Buyer::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'phone_code' => 'nullable|string|max:10',
            'country_id' => 'nullable|exists:countries,id',
            'city' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'sex' => 'nullable|in:Male,Female',
        ]);

        try {
            // Update user
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // Update buyer profile
            if ($buyer) {
                $buyer->update([
                    'phone' => $validated['phone'] ?? null,
                    'phone_code' => $validated['phone_code'] ?? null,
                    'country_id' => $validated['country_id'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'sex' => $validated['sex'] ?? null,
                ]);
            }

            Log::info('Buyer profile updated', [
                'user_id' => $user->id,
            ]);

            return redirect()->route('buyer.account.settings')
                ->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update buyer profile', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update profile. Please try again.');
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
        ], [
            'current_password.required' => 'Current password is required.',
            'password.required' => 'New password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        try {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            Log::info('Buyer password updated', [
                'user_id' => $user->id,
            ]);

            return redirect()->route('buyer.account.settings')
                ->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update buyer password', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->with('error', 'Failed to update password. Please try again.');
        }
    }
}
