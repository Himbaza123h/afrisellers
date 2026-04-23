<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Facades\Log;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = $this->findOrCreateUser($googleUser, 'google');

            Auth::login($user, true);

            return $this->redirectBasedOnRole();

        } catch (Exception $e) {
            Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect()->route('auth.signin')
                ->withErrors(['error' => 'Failed to authenticate with Google. Please try again.']);
        }
    }

    /**
     * Redirect to Facebook
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Handle Facebook callback
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            $user = $this->findOrCreateUser($facebookUser, 'facebook');

            Auth::login($user, true);

            return $this->redirectBasedOnRole();

        } catch (Exception $e) {
            Log::error('Facebook Auth Error: ' . $e->getMessage());
            return redirect()->route('auth.signin')
                ->withErrors(['error' => 'Failed to authenticate with Facebook. Please try again.']);
        }
    }

    /**
     * Find or create user
     */
    private function findOrCreateUser($socialUser, $provider)
    {
        // Check if user exists with this provider
        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($user) {
            return $user;
        }

        // Check if user exists with this email
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Update existing user with provider info
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);
            return $user;
        }

        // Create new user
        return User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
            'password' => Hash::make(Str::random(24)), // Random password
            'email_verified_at' => now(), // Auto-verify social login emails
        ]);
    }

    /**
     * Redirect based on user role (same as LoginController)
     */
    protected function redirectBasedOnRole()
    {
        $user = auth()->user();

        // Admin check
        $isAdmin = $user->roles()
            ->where('roles.id', 1)
            ->where('roles.name', 'Admin')
            ->where('roles.slug', 'admin')
            ->exists();

        if ($isAdmin) {
            return redirect()->route('admin.dashboard.home')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Regional Admin
        $isRegionalAdmin = $user->roles()
            ->where('roles.slug', 'regional_admin')
            ->exists();

        if ($isRegionalAdmin) {
            return redirect()->route('regional.dashboard.home')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Country Admin
        $isCountryAdmin = $user->roles()
            ->where('roles.slug', 'country_admin')
            ->exists();

        if ($isCountryAdmin) {
            return redirect()->route('country.dashboard.home')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Agent
        $isAgent = $user->roles()
            ->where('roles.slug', 'agent')
            ->exists();

        if ($isAgent) {
            return redirect()->route('agent.dashboard.home')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Vendor
        $vendor = \App\Models\Vendor\Vendor::where('user_id', $user->id)->first();

        if ($vendor) {
            return redirect()->route('vendor.dashboard.home')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Default: Buyer
        return redirect()->route('buyer.dashboard.home')
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }
}
