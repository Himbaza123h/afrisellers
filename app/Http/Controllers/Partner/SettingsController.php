<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        return view('partner.settings.index');
    }

    public function general()
    {
        $user = auth()->user();
        return view('partner.settings.general', compact('user'));
    }

    public function updateGeneral(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
        ]);

        $user->update($request->only('name', 'email', 'phone'));

        return redirect()->route('partner.settings.general')
                         ->with('success', 'General settings updated.');
    }

    public function security()
    {
        return view('partner.settings.security');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('partner.settings.security')
                         ->with('success', 'Password changed successfully.');
    }

    public function notifications()
    {
        $user = auth()->user();
        return view('partner.settings.notifications', compact('user'));
    }

    public function updateNotifications(Request $request)
    {
        $user = auth()->user();

        // Store notification preferences as JSON in user meta or a separate table.
        // For now, we use a simple approach via user preferences column (adjust as needed).
        $prefs = [
            'email_messages'       => $request->boolean('email_messages'),
            'email_updates'        => $request->boolean('email_updates'),
            'email_support'        => $request->boolean('email_support'),
            'browser_messages'     => $request->boolean('browser_messages'),
            'browser_updates'      => $request->boolean('browser_updates'),
        ];

        // If users table has a preferences JSON column:
        // $user->update(['preferences' => $prefs]);
        // Otherwise store in cache/session for now:
        session(['partner_notification_prefs_' . $user->id => $prefs]);

        return redirect()->route('partner.settings.notifications')
                         ->with('success', 'Notification preferences saved.');
    }
}
