<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function show(Request $request)
    {
        $partner = auth()->user()->partnerRequest;

        $allServices = is_array($partner?->services) ? $partner->services : [];

        // Service search filter
        $serviceSearch = $request->input('search', '');
        $services = $serviceSearch
            ? array_values(array_filter(
                $allServices,
                fn($s) => str_contains(strtolower($s), strtolower($serviceSearch))
              ))
            : $allServices;

        // Stats
        $stats = [
            'industry'       => $partner?->industry ?? null,
            'business_type'  => $partner?->business_type ?? null,
            'services_count' => count($allServices),
        ];

        return view('partner.business.show', compact('partner', 'services', 'allServices', 'stats', 'serviceSearch'));
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
            $validated['services'] = array_values(array_filter(
                array_map('trim', explode(',', $validated['services_raw']))
            ));
        } else {
            $validated['services'] = [];
        }

        unset($validated['services_raw']);

        $partner->update($validated);

        return redirect()->route('partner.business.show')
                         ->with('success', 'Business information updated successfully.');
    }
}
