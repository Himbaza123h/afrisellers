<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PartnerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PartnerRequestController extends Controller
{
    public function show()
    {
        return view('frontend.partner-request.form');
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'company_name'       => 'required|string|max:255',
        'contact_name'       => 'required|string|max:255',
        'name'               => 'required|string|max:255',
        'email'              => 'required|email|max:255',
        'phone'              => 'nullable|string|max:30',
        'website_url'        => 'nullable|url|max:500',
        'password'           => 'required|string|min:8|confirmed',
        'industry'           => 'nullable|string|max:255',
        'country'            => 'nullable|string|max:255',
        'presence_countries' => 'nullable|integer|min:1|max:54',
        'established'        => 'nullable|integer|min:1800|max:' . date('Y'),
        'about_us'           => 'nullable|string|max:5000',
        'services'           => 'nullable|array',
        'services.*'         => 'string|max:100',
        'partner_type'       => 'nullable|string|max:255',
        'message'            => 'required|string|min:10|max:3000',
        'logo'               => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
        'intro'              => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,webm|max:51200',
    ]);

    $validated['password'] = Hash::make($validated['password']);

    if ($request->hasFile('logo')) {
        $validated['logo'] = $request->file('logo')->store('partner-requests/logos', 'public');
    }

    if ($request->hasFile('intro')) {
        $validated['intro'] = $request->file('intro')->store('partner-requests/intros', 'public');
    }

    // services comes in as a comma-separated string from the tag input
    if ($request->filled('services_raw')) {
        $validated['services'] = array_filter(array_map('trim', explode(',', $request->input('services_raw'))));
    }

    // 1. Create the User first
    $user = User::create([
        'name'       => $validated['name'],
        'email'      => $validated['email'],
        'password'   => $validated['password'],
        'is_partner' => false,
    ]);

    // 2. Create the PartnerRequest with the user_id
    PartnerRequest::create(array_merge($validated, [
        'user_id' => $user->id,
    ]));


    return redirect()->route('partner.request.success');
}

    public function success()
    {
        return view('frontend.partner-request.success');
    }
}
