<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // ─── SHOW ─────────────────────────────────────────────────────────
    public function show()
    {
        $user            = auth()->user()->load('businessProfile.country');
        $businessProfile = $user->businessProfile;

        return view('agent.profile.show', compact('user', 'businessProfile'));
    }

    // ─── EDIT ─────────────────────────────────────────────────────────
    public function edit()
    {
        $user      = auth()->user();
        $countries = Country::orderBy('name')->get();

        return view('agent.profile.edit', compact('user', 'countries'));
    }

    // ─── UPDATE PERSONAL ──────────────────────────────────────────────
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'current_password'      => 'nullable|string',
            'password'              => 'nullable|string|min:8|confirmed',
        ]);

        // Password change
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
        }

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    // ─── UPDATE AVATAR ────────────────────────────────────────────────
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = auth()->user();

        // Delete old avatar
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store(
            'avatars/' . $user->id,
            'public'
        );

        $user->update(['avatar' => $path]);

        return back()->with('success', 'Avatar updated successfully.');
    }

    // ─── DELETE AVATAR ────────────────────────────────────────────────
    public function deleteAvatar()
    {
        $user = auth()->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return back()->with('success', 'Avatar removed.');
    }

    // ─── BUSINESS PROFILE ─────────────────────────────────────────────
    public function businessProfile()
    {
        $user            = auth()->user()->load('businessProfile.country');
        $businessProfile = $user->businessProfile;
        $countries       = Country::orderBy('name')->get();

        return view('agent.profile.business', compact('user', 'businessProfile', 'countries'));
    }

    // ─── UPDATE BUSINESS ──────────────────────────────────────────────
    public function updateBusiness(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'business_name'       => 'required|string|max:255',
            'phone'               => 'nullable|string|max:30',
            'phone_code'          => 'nullable|string|max:10',
            'whatsapp_number'     => 'nullable|string|max:30',
            'business_email'      => 'nullable|email|max:255',
            'city'                => 'nullable|string|max:100',
            'address'             => 'nullable|string|max:500',
            'postal_code'         => 'nullable|string|max:20',
            'country_id'          => 'nullable|exists:countries,id',
            'website'             => 'nullable|url|max:255',
            'description'         => 'nullable|string|max:3000',
            'year_established'    => 'nullable|integer|min:1900|max:' . date('Y'),
            'business_type'       => 'nullable|string|max:100',
            'company_size'        => 'nullable|string|max:100',
            'facebook_link'       => 'nullable|url|max:255',
            'twitter_link'        => 'nullable|url|max:255',
            'linkedin_link'       => 'nullable|url|max:255',
            'instagram_link'      => 'nullable|url|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_position' => 'nullable|string|max:100',
            'logo'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $bp = $user->businessProfile;
            if ($bp?->logo && Storage::disk('public')->exists($bp->logo)) {
                Storage::disk('public')->delete($bp->logo);
            }
            $validated['logo'] = $request->file('logo')->store(
                'business-logos/' . $user->id, 'public'
            );
        }

        BusinessProfile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return back()->with('success', 'Business profile updated successfully.');
    }
}
