<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Country;
use Illuminate\Support\Facades\Log;
use App\Models\Vendor\Vendor;

class LoginController extends Controller
{
    public function index()
    {
        // Redirect if already logged in
        if (auth()->check()) {
            return $this->redirectBasedOnRole();
        }

        return view('frontend.auth.login');
    }

    public function login(Request $request)
    {
        Log::info('=== LOGIN ATTEMPT ===');
        Log::info('Request Data:', [
            'email' => $request->email,
            'ip_address' => $request->ip(),
        ]);

        $validated = $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ],
            [
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 8 characters.',
            ],
        );

        // Find user
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            Log::warning('Login Failed - User Not Found', [
                'email' => $validated['email'],
                'ip_address' => $request->ip(),
            ]);

            return back()
                ->withErrors([
                    'email' => 'These credentials do not match our records.',
                ])
                ->withInput($request->only('email'));
        }

        Log::info('User Found', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);

        // Check if user is a vendor
        $vendor = Vendor::where('user_id', $user->id)->first();

        if ($vendor) {
            Log::info('User is a Vendor', [
                'vendor_id' => $vendor->id,
                'email_verified' => $vendor->email_verified,
                'account_status' => $vendor->account_status,
            ]);

            // Check if email is verified
            if (!$vendor->email_verified) {
                Log::warning('Login Failed - Email Not Verified', [
                    'vendor_id' => $vendor->id,
                    'user_id' => $user->id,
                ]);

                return back()
                    ->withErrors([
                        'email' => 'Please verify your email address first. Check your inbox for the verification link.',
                    ])
                    ->withInput($request->only('email'));
            }

            // Check if account is active
            if ($vendor->account_status !== 'active') {
                Log::warning('Login Failed - Account Not Active', [
                    'vendor_id' => $vendor->id,
                    'account_status' => $vendor->account_status,
                ]);

                return back()
                    ->withErrors([
                        'email' => 'Your vendor account is currently ' . $vendor->account_status . '. Please contact support.',
                    ])
                    ->withInput($request->only('email'));
            }
        }

        // Attempt login
        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            Log::info('Login Successful', [
                'user_id' => auth()->user()->id,
                'email' => auth()->user()->email,
                'remember' => $remember,
            ]);

            Log::info('=== LOGIN SUCCESSFUL ===');

            return $this->redirectBasedOnRole();
        }

        Log::warning('Login Failed - Invalid Password', [
            'user_id' => $user->id,
            'email' => $validated['email'],
            'ip_address' => $request->ip(),
        ]);

        return back()
            ->withErrors([
                'email' => 'These credentials do not match our records.',
            ])
            ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Log::info('=== USER LOGOUT ===', [
            'user_id' => auth()->user()->id ?? null,
            'email' => auth()->user()->email ?? null,
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Logout Successful');

        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }

    public function register()
    {
        // Redirect if already logged in
        if (auth()->check()) {
            return $this->redirectBasedOnRole();
        }

        // Get active countries from database
        $countries = Country::where('status', 'active')->orderBy('name')->get();

        return view('frontend.auth.register', compact('countries'));
    }

    /**
     * Redirect user based on their role
     */
protected function redirectBasedOnRole()
{
    $user = auth()->user();

    // Check if user is an Admin
    $isAdmin = $user->roles()
        ->where('roles.id', 1)
        ->where('roles.name', 'Admin')
        ->where('roles.slug', 'admin')
        ->exists();

    if ($isAdmin) {
        Log::info('Redirecting to Admin Dashboard', [
            'user_id' => $user->id,
            'role' => 'Admin',
        ]);
        return redirect()->route('admin.dashboard.home')
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    // Check if user is a Regional Admin
    $isRegionalAdmin = $user->roles()
        ->where('roles.slug', 'regional_admin')
        ->exists();

    if ($isRegionalAdmin) {
        Log::info('Redirecting to Regional Admin Dashboard', [
            'user_id' => $user->id,
            'role' => 'Regional Admin',
        ]);
        return redirect()->route('regional.dashboard.home')
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    // Check if user is a Country Admin
    $isCountryAdmin = $user->roles()
        ->where('roles.slug', 'country_admin')
        ->exists();

    if ($isCountryAdmin) {
        Log::info('Redirecting to Country Admin Dashboard', [
            'user_id' => $user->id,
            'role' => 'Country Admin',
        ]);
        return redirect()->route('country.dashboard.home')
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    // Check if user is an Agent
    $isAgent = $user->roles()
        ->where('roles.slug', 'agent')
        ->exists();

    if ($isAgent) {
        Log::info('Redirecting to Agent Dashboard', [
            'user_id' => $user->id,
            'role' => 'Agent',
        ]);
        return redirect()->route('agent.dashboard.home')
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    // Check if user is a vendor
    $vendor = Vendor::where('user_id', $user->id)->first();

    if ($vendor) {
        Log::info('Redirecting to Vendor Dashboard', [
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
        ]);
        return redirect()->route('vendor.dashboard.home')
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    // Default redirect for buyers/customers
    Log::info('Redirecting to Buyer Dashboard', [
        'user_id' => $user->id,
    ]);
    return redirect()->route('buyer.dashboard.home')
        ->with('success', 'Welcome back, ' . $user->name . '!');
}

public function regionalTokenLogin($token)
{
    try {
        // Get user ID from cache
        $userId = \Illuminate\Support\Facades\Cache::get('regional_login_token_' . $token);

        if (!$userId) {
            Log::warning('Invalid or expired regional login token', ['token' => $token]);
            return redirect()->route('auth.signin')
                ->withErrors(['email' => 'Invalid or expired login link. Please try again.']);
        }

        // Find user
        $user = User::find($userId);

        if (!$user) {
            Log::warning('User not found for regional login', ['user_id' => $userId]);
            return redirect()->route('auth.signin')
                ->withErrors(['email' => 'User not found.']);
        }

        // Check if user has regional_admin role
        $isRegionalAdmin = $user->roles()
            ->where('roles.slug', 'regional_admin')
            ->exists();

        if (!$isRegionalAdmin) {
            Log::warning('User does not have regional admin role', ['user_id' => $userId]);
            return redirect()->route('auth.signin')
                ->withErrors(['email' => 'You do not have Regional Admin access.']);
        }

        // Delete the token
        \Illuminate\Support\Facades\Cache::forget('regional_login_token_' . $token);

        // Log in the user
        Auth::login($user);
        request()->session()->regenerate();

        Log::info('Regional Admin Token Login Successful', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Redirect to regional dashboard
        return redirect()->route('regional.dashboard.home')
            ->with('success', 'Welcome to Regional Admin Dashboard, ' . $user->name . '!');

    } catch (\Exception $e) {
        Log::error('Regional Token Login Error: ' . $e->getMessage());
        return redirect()->route('auth.signin')
            ->withErrors(['email' => 'An error occurred during login. Please try again.']);
    }
}

public function vendorTokenLogin($token)
{
    return $this->handleTokenLogin($token, 'vendor_login_token_', 'vendor', 'vendor.dashboard.home');
}
public function countryTokenLogin($token)
{
    return $this->handleTokenLogin($token, 'country_login_token_', 'country_admin', 'country.dashboard.home');
}

public function buyerTokenLogin($token)
{
    return $this->handleTokenLogin($token, 'buyer_login_token_', 'buyer', 'buyer.dashboard.home');
}

public function agentTokenLogin($token)
{
    return $this->handleTokenLogin($token, 'agent_login_token_', 'agent', 'agent.dashboard.home');
}

// Generic token login handler
private function handleTokenLogin($token, $cachePrefix, $roleSlug, $dashboardRoute)
{
    try {
        $userId = \Illuminate\Support\Facades\Cache::get($cachePrefix . $token);

        if (!$userId) {
            Log::warning('Invalid or expired login token', ['token' => $token]);
            return redirect()->route('auth.signin')
                ->withErrors(['email' => 'Invalid or expired login link.']);
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('auth.signin')
                ->withErrors(['email' => 'User not found.']);
        }

        $hasRole = $user->roles()->where('roles.slug', $roleSlug)->exists();
        if (!$hasRole) {
            return redirect()->route('auth.signin')
                ->withErrors(['email' => 'You do not have required access.']);
        }

        \Illuminate\Support\Facades\Cache::forget($cachePrefix . $token);

        Auth::login($user);
        request()->session()->regenerate();

        Log::info('Token Login Successful', [
            'user_id' => $user->id,
            'role' => $roleSlug,
        ]);

        return redirect()->route($dashboardRoute)
            ->with('success', 'Welcome, ' . $user->name . '!');

    } catch (\Exception $e) {
        Log::error('Token Login Error: ' . $e->getMessage());
        return redirect()->route('auth.signin')
            ->withErrors(['email' => 'Login failed.']);
    }
}
}
