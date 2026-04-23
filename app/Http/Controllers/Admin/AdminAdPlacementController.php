<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdMedia;
use App\Models\AdPlacement;
use App\Models\FallbackAd;
use Illuminate\Http\Request;

class AdminAdPlacementController extends Controller
{
    // ── Index — show all positions and what's assigned ────────────
    public function index()
    {
        $positions  = FallbackAd::positions();

        // Load one active placement per position (latest)
        $placements = AdPlacement::with('media')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('position');

        return view('admin.ad-placements.index', compact('positions', 'placements'));
    }

    // ── Create — library picker ───────────────────────────────────
    public function create(Request $request)
    {
        $positions  = FallbackAd::positions();
        $prePosition = $request->get('position'); // optional pre-select

        $media = AdMedia::latest()->paginate(24);

        return view('admin.ad-placements.create', compact('positions', 'media', 'prePosition'));
    }

    // ── Store ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $positions = array_keys(FallbackAd::positions());

        $request->validate([
            'ad_media_id' => ['required', 'exists:ad_media,id'],
            'position'    => ['required', 'in:' . implode(',', $positions)],
            'cta_url'     => ['nullable', 'url', 'max:500'],
            'headline'    => ['nullable', 'string', 'max:200'],
            'sub_text'    => ['nullable', 'string', 'max:300'],
            'starts_at'   => ['nullable', 'date'],
            'ends_at'     => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'   => ['boolean'],
        ]);

        AdPlacement::create([
            'ad_media_id' => $request->ad_media_id,
            'position'    => $request->position,
            'cta_url'     => $request->cta_url,
            'headline'    => $request->headline,
            'sub_text'    => $request->sub_text,
            'is_active'   => $request->boolean('is_active', true),
            'sort_order'  => AdPlacement::where('position', $request->position)->max('sort_order') + 1,
            'starts_at'   => $request->starts_at,
            'ends_at'     => $request->ends_at,
            'created_by'  => auth()->id(),
        ]);

        return redirect()->route('admin.ad-placements.index')
            ->with('success', 'Ad placement created successfully.');
    }

    // ── Edit ─────────────────────────────────────────────────────
    public function edit(AdPlacement $adPlacement)
    {
        $positions   = FallbackAd::positions();
        $media       = AdMedia::latest()->paginate(24);
        $prePosition = $adPlacement->position;

        return view('admin.ad-placements.create', compact('positions', 'media', 'prePosition', 'adPlacement'));
    }

    // ── Update ────────────────────────────────────────────────────
    public function update(Request $request, AdPlacement $adPlacement)
    {
        $positions = array_keys(FallbackAd::positions());

        $request->validate([
            'ad_media_id' => ['required', 'exists:ad_media,id'],
            'position'    => ['required', 'in:' . implode(',', $positions)],
            'cta_url'     => ['nullable', 'url', 'max:500'],
            'headline'    => ['nullable', 'string', 'max:200'],
            'sub_text'    => ['nullable', 'string', 'max:300'],
            'starts_at'   => ['nullable', 'date'],
            'ends_at'     => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'   => ['boolean'],
        ]);

        $adPlacement->update([
            'ad_media_id' => $request->ad_media_id,
            'position'    => $request->position,
            'cta_url'     => $request->cta_url,
            'headline'    => $request->headline,
            'sub_text'    => $request->sub_text,
            'is_active'   => $request->boolean('is_active', true),
            'starts_at'   => $request->starts_at,
            'ends_at'     => $request->ends_at,
        ]);

        return redirect()->route('admin.ad-placements.index')
            ->with('success', 'Placement updated.');
    }

    // ── Toggle Active ─────────────────────────────────────────────
    public function toggleActive(AdPlacement $adPlacement)
    {
        $adPlacement->update(['is_active' => ! $adPlacement->is_active]);

        return back()->with('success', 'Placement ' . ($adPlacement->is_active ? 'activated' : 'deactivated') . '.');
    }

    // ── Destroy ───────────────────────────────────────────────────
    public function destroy(AdPlacement $adPlacement)
    {
        $adPlacement->delete();

        return back()->with('success', 'Placement removed.');
    }
}
