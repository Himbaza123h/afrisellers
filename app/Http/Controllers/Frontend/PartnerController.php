<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerRequest;

class PartnerController extends Controller
{
    public function show($id, $name)
    {
        $partner = PartnerRequest::where('status', 'approved')->findOrFail($id);

        return view('frontend.partners.show', compact('partner'));
    }
}
