<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of all regions.
     */
    public function index()
    {
        $regions = Region::active()
            ->withCount('countries')
            ->orderBy('name', 'asc')
            ->paginate(12);

        return view('frontend.region.index', compact('regions'));
    }

    /**
     * Display countries within a specific region.
     */
    public function countries(Region $region)
    {
        $countries = Country::where('region_id', $region->id)
            ->active()
            ->orderBy('name', 'asc')
            ->paginate(20);

        return view('frontend.region.countries', compact('region', 'countries'));
    }
}
