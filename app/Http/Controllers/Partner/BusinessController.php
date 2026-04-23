<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function show()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.business.show', compact('partner'));
    }

    public function edit()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.business.edit', compact('partner'));
    }

    public function update(Request $request)
    {
        $partner = auth()->user()->partnerRequest;

        $validated = $request->validate([
            'industry'      => 'nullable|string|max:255',
            'business_type' => 'nullable|in:Corporation,SME,Startup',
            'services_raw'  => 'nullable|string',
        ]);

        if (!empty($validated['services_raw'])) {
            $validated['services'] = array_filter(
                array_map('trim', explode(',', $validated['services_raw']))
            );
        }

        unset($validated['services_raw']);

        $partner->update($validated);

        return redirect()->route('partner.business.show')
                         ->with('success', 'Business information updated.');
    }
}
