<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Display settings dashboard
     */
    public function index()
    {
        $settings = [
            'site_name' => SystemSetting::get('site_name', config('app.name')),
            'site_email' => SystemSetting::get('site_email', 'admin@example.com'),
            'site_phone' => SystemSetting::get('site_phone', ''),
            'site_address' => SystemSetting::get('site_address', ''),
            'timezone' => SystemSetting::get('timezone', 'UTC'),
            'currency' => SystemSetting::get('currency', 'USD'),
            'currency_symbol' => SystemSetting::get('currency_symbol', '$'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Display general settings
     */
    public function general()
    {
        $settings = [
            'site_name' => SystemSetting::get('site_name', config('app.name')),
            'site_tagline' => SystemSetting::get('site_tagline', ''),
            'site_description' => SystemSetting::get('site_description', ''),
            'site_email' => SystemSetting::get('site_email', 'admin@example.com'),
            'site_phone' => SystemSetting::get('site_phone', ''),
            'site_address' => SystemSetting::get('site_address', ''),
            'site_logo' => SystemSetting::get('site_logo', ''),
            'site_favicon' => SystemSetting::get('site_favicon', ''),
            'timezone' => SystemSetting::get('timezone', 'UTC'),
            'date_format' => SystemSetting::get('date_format', 'Y-m-d'),
            'time_format' => SystemSetting::get('time_format', 'H:i:s'),
            'currency' => SystemSetting::get('currency', 'USD'),
            'currency_symbol' => SystemSetting::get('currency_symbol', '$'),
            'currency_position' => SystemSetting::get('currency_position', 'left'),
            'items_per_page' => SystemSetting::get('items_per_page', 20),
            'maintenance_mode' => SystemSetting::get('maintenance_mode', false),
            'allow_registration' => SystemSetting::get('allow_registration', true),
        ];

        $timezones = timezone_identifiers_list();

        return view('admin.settings.general', compact('settings', 'timezones'));
    }

    /**
     * Display email settings
     */
    public function email()
    {
        $settings = [
            'mail_driver' => SystemSetting::get('mail_driver', 'smtp'),
            'mail_host' => SystemSetting::get('mail_host', 'smtp.mailtrap.io'),
            'mail_port' => SystemSetting::get('mail_port', 2525),
            'mail_username' => SystemSetting::get('mail_username', ''),
            'mail_password' => SystemSetting::get('mail_password', ''),
            'mail_encryption' => SystemSetting::get('mail_encryption', 'tls'),
            'mail_from_address' => SystemSetting::get('mail_from_address', 'noreply@example.com'),
            'mail_from_name' => SystemSetting::get('mail_from_name', config('app.name')),
        ];

        return view('admin.settings.email', compact('settings'));
    }

    /**
     * Display payment settings
     */
    public function payment()
    {
        $settings = [
            // Payment Gateway Settings
            'payment_gateway' => SystemSetting::get('payment_gateway', 'stripe'),
            'enable_stripe' => SystemSetting::get('enable_stripe', false),
            'stripe_public_key' => SystemSetting::get('stripe_public_key', ''),
            'stripe_secret_key' => SystemSetting::get('stripe_secret_key', ''),
            'enable_paypal' => SystemSetting::get('enable_paypal', false),
            'paypal_client_id' => SystemSetting::get('paypal_client_id', ''),
            'paypal_secret' => SystemSetting::get('paypal_secret', ''),
            'paypal_mode' => SystemSetting::get('paypal_mode', 'sandbox'),
            'enable_bank_transfer' => SystemSetting::get('enable_bank_transfer', true),
            'bank_details' => SystemSetting::get('bank_details', ''),

            // Commission Settings
            'platform_commission_rate' => SystemSetting::get('platform_commission_rate', 10),
            'min_commission_amount' => SystemSetting::get('min_commission_amount', 1),
            'agent_commission_rate' => SystemSetting::get('agent_commission_rate', 5),
            'enable_escrow' => SystemSetting::get('enable_escrow', true),
            'escrow_release_days' => SystemSetting::get('escrow_release_days', 7),

            // Tax Settings
            'enable_tax' => SystemSetting::get('enable_tax', false),
            'tax_rate' => SystemSetting::get('tax_rate', 0),
            'tax_name' => SystemSetting::get('tax_name', 'VAT'),
        ];

        return view('admin.settings.payment', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $section = $request->input('section', 'general');

        switch ($section) {
            case 'general':
                return $this->updateGeneral($request);
            case 'email':
                return $this->updateEmail($request);
            case 'payment':
                return $this->updatePayment($request);
            default:
                return back()->with('error', 'Invalid section');
        }
    }

    /**
     * Update general settings
     */
    private function updateGeneral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'site_email' => 'required|email',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string|max:500',
            'timezone' => 'required|string',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:5',
            'currency_position' => 'required|in:left,right',
            'items_per_page' => 'required|integer|min:5|max:100',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'site_favicon' => 'nullable|image|mimes:ico,png|max:1024',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $oldLogo = SystemSetting::get('site_logo');
            if ($oldLogo && Storage::exists('public/' . $oldLogo)) {
                Storage::delete('public/' . $oldLogo);
            }

            $logo = $request->file('site_logo');
            $logoPath = $logo->store('settings/logos', 'public');
            SystemSetting::set('site_logo', $logoPath, 'string', 'Site Logo');
        }

        // Handle favicon upload
        if ($request->hasFile('site_favicon')) {
            $oldFavicon = SystemSetting::get('site_favicon');
            if ($oldFavicon && Storage::exists('public/' . $oldFavicon)) {
                Storage::delete('public/' . $oldFavicon);
            }

            $favicon = $request->file('site_favicon');
            $faviconPath = $favicon->store('settings/favicons', 'public');
            SystemSetting::set('site_favicon', $faviconPath, 'string', 'Site Favicon');
        }

        // Save text settings
        SystemSetting::set('site_name', $request->site_name, 'string', 'Site Name');
        SystemSetting::set('site_tagline', $request->site_tagline, 'string', 'Site Tagline');
        SystemSetting::set('site_description', $request->site_description, 'string', 'Site Description');
        SystemSetting::set('site_email', $request->site_email, 'string', 'Site Email');
        SystemSetting::set('site_phone', $request->site_phone, 'string', 'Site Phone');
        SystemSetting::set('site_address', $request->site_address, 'string', 'Site Address');
        SystemSetting::set('timezone', $request->timezone, 'string', 'Timezone');
        SystemSetting::set('date_format', $request->date_format, 'string', 'Date Format');
        SystemSetting::set('time_format', $request->time_format, 'string', 'Time Format');
        SystemSetting::set('currency', $request->currency, 'string', 'Currency');
        SystemSetting::set('currency_symbol', $request->currency_symbol, 'string', 'Currency Symbol');
        SystemSetting::set('currency_position', $request->currency_position, 'string', 'Currency Position');
        SystemSetting::set('items_per_page', $request->items_per_page, 'integer', 'Items Per Page');
        SystemSetting::set('maintenance_mode', $request->has('maintenance_mode'), 'boolean', 'Maintenance Mode');
        SystemSetting::set('allow_registration', $request->has('allow_registration'), 'boolean', 'Allow Registration');

        return back()->with('success', 'General settings updated successfully!');
    }

    /**
     * Update email settings
     */
    private function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mail_driver' => 'required|string|in:smtp,sendmail,mailgun,ses,postmark',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        SystemSetting::set('mail_driver', $request->mail_driver, 'string', 'Mail Driver');
        SystemSetting::set('mail_host', $request->mail_host, 'string', 'Mail Host');
        SystemSetting::set('mail_port', $request->mail_port, 'integer', 'Mail Port');
        SystemSetting::set('mail_username', $request->mail_username, 'string', 'Mail Username');
        SystemSetting::set('mail_password', $request->mail_password, 'string', 'Mail Password');
        SystemSetting::set('mail_encryption', $request->mail_encryption, 'string', 'Mail Encryption');
        SystemSetting::set('mail_from_address', $request->mail_from_address, 'string', 'Mail From Address');
        SystemSetting::set('mail_from_name', $request->mail_from_name, 'string', 'Mail From Name');

        return back()->with('success', 'Email settings updated successfully!');
    }

    /**
     * Update payment settings
     */
    private function updatePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_gateway' => 'required|string',
            'stripe_public_key' => 'nullable|string',
            'stripe_secret_key' => 'nullable|string',
            'paypal_client_id' => 'nullable|string',
            'paypal_secret' => 'nullable|string',
            'paypal_mode' => 'nullable|in:sandbox,live',
            'platform_commission_rate' => 'required|numeric|min:0|max:100',
            'min_commission_amount' => 'required|numeric|min:0',
            'agent_commission_rate' => 'required|numeric|min:0|max:100',
            'escrow_release_days' => 'required|integer|min:1|max:90',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Payment Gateway Settings
        SystemSetting::set('payment_gateway', $request->payment_gateway, 'string', 'Payment Gateway');
        SystemSetting::set('enable_stripe', $request->has('enable_stripe'), 'boolean', 'Enable Stripe');
        SystemSetting::set('stripe_public_key', $request->stripe_public_key, 'string', 'Stripe Public Key');
        SystemSetting::set('stripe_secret_key', $request->stripe_secret_key, 'string', 'Stripe Secret Key');
        SystemSetting::set('enable_paypal', $request->has('enable_paypal'), 'boolean', 'Enable PayPal');
        SystemSetting::set('paypal_client_id', $request->paypal_client_id, 'string', 'PayPal Client ID');
        SystemSetting::set('paypal_secret', $request->paypal_secret, 'string', 'PayPal Secret');
        SystemSetting::set('paypal_mode', $request->paypal_mode, 'string', 'PayPal Mode');
        SystemSetting::set('enable_bank_transfer', $request->has('enable_bank_transfer'), 'boolean', 'Enable Bank Transfer');
        SystemSetting::set('bank_details', $request->bank_details, 'string', 'Bank Details');

        // Commission Settings
        SystemSetting::set('platform_commission_rate', $request->platform_commission_rate, 'decimal', 'Platform Commission Rate');
        SystemSetting::set('min_commission_amount', $request->min_commission_amount, 'decimal', 'Minimum Commission Amount');
        SystemSetting::set('agent_commission_rate', $request->agent_commission_rate, 'decimal', 'Agent Commission Rate');
        SystemSetting::set('enable_escrow', $request->has('enable_escrow'), 'boolean', 'Enable Escrow');
        SystemSetting::set('escrow_release_days', $request->escrow_release_days, 'integer', 'Escrow Release Days');

        // Tax Settings
        SystemSetting::set('enable_tax', $request->has('enable_tax'), 'boolean', 'Enable Tax');
        SystemSetting::set('tax_rate', $request->tax_rate ?? 0, 'decimal', 'Tax Rate');
        SystemSetting::set('tax_name', $request->tax_name, 'string', 'Tax Name');

        return back()->with('success', 'Payment settings updated successfully!');
    }
}
