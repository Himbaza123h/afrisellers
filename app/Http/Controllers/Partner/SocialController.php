<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    public function show()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.social.show', compact('partner'));
    }

    public function edit()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.social.edit', compact('partner'));
    }

    public function update(Request $request)
    {
        $partner = auth()->user()->partnerRequest;

        $validated = $request->validate([
            'facebook_url'  => 'nullable|url|max:500',
            'instagram_url' => 'nullable|url|max:500',
            'twitter_url'   => 'nullable|url|max:500',
            'linkedin_url'  => 'nullable|url|max:500',
            'youtube_url'   => 'nullable|url|max:500',
            'tiktok_url'    => 'nullable|url|max:500',
        ]);

        $partner->update($validated);

        return redirect()->route('partner.social.show')
                         ->with('success', 'Social media profiles updated.');
    }
}
