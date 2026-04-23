<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    public function show()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.branding.show', compact('partner'));
    }

    public function edit()
    {
        $partner = auth()->user()->partnerRequest;
        return view('partner.branding.edit', compact('partner'));
    }

    public function update(Request $request)
    {
        $partner = auth()->user()->partnerRequest;

        $validated = $request->validate([
            'short_description' => 'nullable|string|max:300',
            'full_description'  => 'nullable|string|max:5000',
            'promo_video_url'   => 'nullable|url|max:500',
        ]);

        $partner->update($validated);

        return redirect()->route('partner.branding.show')
                         ->with('success', 'Branding updated.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|file|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
        ]);

        $partner = auth()->user()->partnerRequest;

        if ($partner->logo) {
            Storage::disk('public')->delete($partner->logo);
        }

        $partner->update([
            'logo' => $request->file('logo')->store('partners/logos', 'public'),
        ]);

        return back()->with('success', 'Logo uploaded.');
    }

    public function deleteLogo()
    {
        $partner = auth()->user()->partnerRequest;

        if ($partner->logo) {
            Storage::disk('public')->delete($partner->logo);
            $partner->update(['logo' => null]);
        }

        return back()->with('success', 'Logo removed.');
    }

    public function uploadCover(Request $request)
    {
        $request->validate([
            'cover' => 'required|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        $partner = auth()->user()->partnerRequest;

        if ($partner->cover_image) {
            Storage::disk('public')->delete($partner->cover_image);
        }

        $partner->update([
            'cover_image' => $request->file('cover')->store('partners/covers', 'public'),
        ]);

        return back()->with('success', 'Cover image uploaded.');
    }

    public function deleteCover()
    {
        $partner = auth()->user()->partnerRequest;

        if ($partner->cover_image) {
            Storage::disk('public')->delete($partner->cover_image);
            $partner->update(['cover_image' => null]);
        }

        return back()->with('success', 'Cover image removed.');
    }
}
