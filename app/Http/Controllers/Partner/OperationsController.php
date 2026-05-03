<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    public function show(Request $request)
    {
        $partner = auth()->user()->partnerRequest;

        $allCountries = is_array($partner?->countries_of_operation)
            ? $partner->countries_of_operation
            : [];

        // Country search filter
        $countrySearch = $request->input('search', '');
        $countries = $countrySearch
            ? array_values(array_filter(
                $allCountries,
                fn($c) => str_contains(strtolower($c), strtolower($countrySearch))
              ))
            : $allCountries;

        // Stats
        $stats = [
            'presence_countries' => $partner?->presence_countries ?? 0,
            'branches_count'     => $partner?->branches_count ?? 0,
            'target_market'      => $partner?->target_market ?? '—',
            'listed_countries'   => count($allCountries),
        ];

        return view('partner.operations.show', compact('partner', 'countries', 'allCountries', 'stats', 'countrySearch'));
    }

    public function edit()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.operations.edit', compact('partner'));
    }

    public function update(Request $request)
    {
        $partner = auth()->user()->partnerRequest;

        $validated = $request->validate([
            'presence_countries' => 'nullable|integer|min:1|max:54',
            'branches_count'     => 'nullable|integer|min:0',
            'target_market'      => 'nullable|in:Individuals,Businesses,Both',
            'countries_raw'      => 'nullable|string',
        ]);

        if (!empty($validated['countries_raw'])) {
            $validated['countries_of_operation'] = array_values(array_filter(
                array_map('trim', explode(',', $validated['countries_raw']))
            ));
        } else {
            $validated['countries_of_operation'] = [];
        }

        unset($validated['countries_raw']);

        $partner->update($validated);

        return redirect()->route('partner.operations.show')
                         ->with('success', 'Operations updated successfully.');
    }
}
