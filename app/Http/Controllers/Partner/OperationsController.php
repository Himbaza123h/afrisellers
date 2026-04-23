<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    public function show()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.operations.show', compact('partner'));
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
            $validated['countries_of_operation'] = array_filter(
                array_map('trim', explode(',', $validated['countries_raw']))
            );
        }

        unset($validated['countries_raw']);

        $partner->update($validated);

        return redirect()->route('partner.operations.show')
                         ->with('success', 'Operations & presence updated.');
    }
}
