<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PartnerRequest;
use Illuminate\Http\Request;

class PartnerRequestController extends Controller
{
    public function show()
    {
        return view('frontend.partner-request.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'nullable|string|max:30',
            'website_url'  => 'nullable|url|max:500',
            'industry'     => 'nullable|string|max:255',
            'country'      => 'nullable|string|max:255',
            'partner_type' => 'nullable|string|max:255',
            'message'      => 'required|string|min:20|max:3000',
            'logo'         => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('partner-requests', 'public');
        }

        PartnerRequest::create($validated);

        return redirect()->route('partner.request.success');
    }

    public function success()
    {
        return view('frontend.partner-request.success');
    }
}
