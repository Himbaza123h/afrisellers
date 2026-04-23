<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FallbackAd;
use Illuminate\Http\Request;

class FallbackAdController extends Controller
{
    public function index(Request $request)
    {
        $query = FallbackAd::query();

        if ($request->filled('position')) $query->where('position', $request->position);
        if ($request->filled('type'))     $query->where('type', $request->type);

        $ads       = $query->orderBy('position')->orderBy('sort_order')->paginate(20)->withQueryString();
        $positions = FallbackAd::positions();
        $types     = FallbackAd::types();

        return view('admin.fallback-ads.index', compact('ads', 'positions', 'types'));
    }

    public function create()
    {
        $positions = FallbackAd::positions();
        $types     = FallbackAd::types();
        return view('admin.fallback-ads.create', compact('positions', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'position'  => 'required|in:'.implode(',', array_keys(FallbackAd::positions())),
            'type'      => 'required|in:'.implode(',', array_keys(FallbackAd::types())),
            'media'     => 'required_unless:type,text|nullable|url',
            'bg'        => 'required_if:type,text|nullable|string|max:255',
            'headline'  => 'required|string|max:255',
            'sub_text'  => 'nullable|string|max:255',
            'cta_url'   => 'nullable|url',
            'badge'     => 'nullable|string|max:50',
            'overlay'   => 'nullable|string|max:80',
            'accent'    => 'nullable|string|max:30',
            'pattern'   => 'boolean',
            'sort_order'=> 'nullable|integer|min:0',
        ]);

        FallbackAd::create([
            'position'   => $request->position,
            'type'       => $request->type,
            'media'      => $request->media,
            'bg'         => $request->bg,
            'headline'   => $request->headline,
            'sub_text'   => $request->sub_text,
            'cta_url'    => $request->cta_url ?? '#',
            'badge'      => $request->badge,
            'overlay'    => $request->overlay ?? 'rgba(0,0,0,0.55)',
            'accent'     => $request->accent ?? '#ff0808',
            'pattern'    => $request->boolean('pattern'),
            'is_active'  => true,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.fallback-ads.index')
            ->with('success', 'Fallback ad created successfully!');
    }

    public function edit(FallbackAd $fallbackAd)
    {
        $positions = FallbackAd::positions();
        $types     = FallbackAd::types();
        return view('admin.fallback-ads.edit', compact('fallbackAd', 'positions', 'types'));
    }

    public function update(Request $request, FallbackAd $fallbackAd)
    {
        $request->validate([
            'position'  => 'required|in:'.implode(',', array_keys(FallbackAd::positions())),
            'type'      => 'required|in:'.implode(',', array_keys(FallbackAd::types())),
            'media'     => 'required_unless:type,text|nullable|url',
            'bg'        => 'required_if:type,text|nullable|string|max:255',
            'headline'  => 'required|string|max:255',
            'sub_text'  => 'nullable|string|max:255',
            'cta_url'   => 'nullable|url',
            'badge'     => 'nullable|string|max:50',
            'overlay'   => 'nullable|string|max:80',
            'accent'    => 'nullable|string|max:30',
            'pattern'   => 'boolean',
            'sort_order'=> 'nullable|integer|min:0',
        ]);

        $fallbackAd->update([
            'position'   => $request->position,
            'type'       => $request->type,
            'media'      => $request->media,
            'bg'         => $request->bg,
            'headline'   => $request->headline,
            'sub_text'   => $request->sub_text,
            'cta_url'    => $request->cta_url ?? '#',
            'badge'      => $request->badge,
            'overlay'    => $request->overlay ?? 'rgba(0,0,0,0.55)',
            'accent'     => $request->accent ?? '#ff0808',
            'pattern'    => $request->boolean('pattern'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.fallback-ads.index')
            ->with('success', 'Fallback ad updated successfully!');
    }

    public function destroy(FallbackAd $fallbackAd)
    {
        $fallbackAd->delete();
        return back()->with('success', 'Fallback ad deleted.');
    }

    public function toggleStatus(FallbackAd $fallbackAd)
    {
        $fallbackAd->update(['is_active' => !$fallbackAd->is_active]);
        return back()->with('success', 'Status updated.');
    }
}
