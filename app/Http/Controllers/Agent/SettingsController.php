<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    // ─── Shared helper ────────────────────────────────────────────────
    private function settings(): AgentSetting
    {
        return auth()->user()->getAgentSettings();
    }

    // ─── INDEX ────────────────────────────────────────────────────────
    public function index()
    {
        $settings = $this->settings();
        return view('agent.settings.index', compact('settings'));
    }

    // ─── GENERAL ──────────────────────────────────────────────────────
    public function general()
    {
        $settings = $this->settings();
        return view('agent.settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'timezone'    => 'required|string|max:100',
            'language'    => 'required|in:en,fr,es,ar,sw',
            'currency'    => 'required|string|max:10',
            'date_format' => 'required|string|max:30',
        ]);

        $this->settings()->update($validated);

        return back()->with('success', 'General settings updated.');
    }

    // ─── NOTIFICATIONS ────────────────────────────────────────────────
    public function notifications()
    {
        $settings = $this->settings();
        return view('agent.settings.notifications', compact('settings'));
    }

    public function updateNotifications(Request $request)
    {
        $this->settings()->update([
            'notify_email'        => $request->boolean('notify_email'),
            'notify_new_vendor'   => $request->boolean('notify_new_vendor'),
            'notify_commission'   => $request->boolean('notify_commission'),
            'notify_ticket_reply' => $request->boolean('notify_ticket_reply'),
            'notify_payout'       => $request->boolean('notify_payout'),
            'notify_expiry'       => $request->boolean('notify_expiry'),
        ]);

        return back()->with('success', 'Notification preferences updated.');
    }

    // ─── SECURITY ─────────────────────────────────────────────────────
    public function security()
    {
        $settings = $this->settings();
        return view('agent.settings.security', compact('settings'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function toggleTwoFactor(Request $request)
    {
        $settings = $this->settings();

        $settings->update([
            'two_factor_enabled' => !$settings->two_factor_enabled,
        ]);

        $status = $settings->two_factor_enabled ? 'enabled' : 'disabled';

        return back()->with('success', "Two-factor authentication {$status}.");
    }

    // ─── PAYMENT ──────────────────────────────────────────────────────
    public function payment()
    {
        $settings = $this->settings();
        return view('agent.settings.payment', compact('settings'));
    }

    public function updatePayment(Request $request)
    {
        $request->validate([
            'payout_method'          => 'required|in:bank,mobile_money,paypal',
            'bank_name'              => 'nullable|string|max:255',
            'bank_account_number'    => 'nullable|string|max:100',
            'bank_account_name'      => 'nullable|string|max:255',
            'bank_branch'            => 'nullable|string|max:255',
            'mobile_money_number'    => 'nullable|string|max:30',
            'mobile_money_provider'  => 'nullable|string|max:100',
            'paypal_email'           => 'nullable|email|max:255',
        ]);

        $this->settings()->update($request->only([
            'payout_method', 'bank_name', 'bank_account_number',
            'bank_account_name', 'bank_branch',
            'mobile_money_number', 'mobile_money_provider', 'paypal_email',
        ]));

        return back()->with('success', 'Payment settings updated.');
    }

    // ─── COMMISSION ───────────────────────────────────────────────────
    public function commission()
    {
        $settings = $this->settings();
        return view('agent.settings.commission', compact('settings'));
    }

    public function updateCommission(Request $request)
    {
        $request->validate([
            'commission_payout_threshold' => 'required|numeric|min:1',
            'commission_payout_frequency' => 'required|in:weekly,biweekly,monthly',
        ]);

        $this->settings()->update($request->only([
            'commission_payout_threshold',
            'commission_payout_frequency',
        ]));

        return back()->with('success', 'Commission settings updated.');
    }
}
