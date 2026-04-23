<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.contact.show', compact('partner'));
    }

    public function edit()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.contact.edit', compact('partner'));
    }

    public function update(Request $request)
    {
        $partner = auth()->user()->partnerRequest;

        $validated = $request->validate([
            'contact_name'     => 'required|string|max:255',
            'contact_position' => 'nullable|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'nullable|string|max:30',
            'whatsapp'         => 'nullable|string|max:30',
        ]);

        $partner->update($validated);

        return redirect()->route('partner.contact.show')
                         ->with('success', 'Contact details updated.');
    }
}
