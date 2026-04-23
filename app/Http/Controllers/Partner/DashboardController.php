<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.dashboard.index', compact('partner'));
    }

    public function print()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.dashboard.print', compact('partner'));
    }
}
