<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function show()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.company.show', compact('partner'));
    }

    public function edit()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.company.edit', compact('partner'));
    }

    public function update(Request $request)
    {
        $partner = auth()->user()->partnerRequest;

        $validated = $request->validate([
            'company_name'        => 'required|string|max:255',
            'trading_name'        => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'established'         => 'nullable|integer|min:1800|max:' . date('Y'),
            'country'             => 'nullable|string|max:100',
            'physical_address'    => 'nullable|string|max:500',
            'website_url'         => 'nullable|url|max:500',
        ]);

        $partner->update($validated);

        return redirect()->route('partner.company.show')
                         ->with('success', 'Company information updated.');
    }
}
