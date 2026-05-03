<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    /** The 6 social platforms we track. */
    private const PLATFORMS = [
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'linkedin_url',
        'youtube_url',
        'tiktok_url',
    ];

    public function show()
    {
        $partner = auth()->user()->partnerRequest;

        $connected = collect(self::PLATFORMS)
            ->filter(fn($field) => !empty($partner?->{$field}))
            ->count();

        $stats = [
            'total'       => count(self::PLATFORMS),
            'connected'   => $connected,
            'missing'     => count(self::PLATFORMS) - $connected,
        ];

        return view('partner.social.show', compact('partner', 'stats'));
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
                         ->with('success', 'Social media profiles updated successfully.');
    }
}
