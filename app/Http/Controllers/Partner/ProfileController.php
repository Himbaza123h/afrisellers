<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.profile.show', compact('partner'));
    }

    public function edit()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.profile.edit', compact('partner'));
    }
}
