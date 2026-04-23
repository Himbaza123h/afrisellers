<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdMedia;
use App\Models\SquareAd;
use Illuminate\Http\Request;

class SquareAdController extends Controller
{
    public function index(Request $request)
    {
        $query = SquareAd::with('media');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $ads   = $query->orderBy('sort_order')->paginate(20)->withQueryString();
        $types = SquareAd::distinct()->pluck('type')->filter()->values();

        return view('admin.square-ads.index', compact('ads', 'types'));
    }

    public function create()
    {
        $library = AdMedia::orderBy('name')->get();
        return view('admin.square-ads.create', compact('library'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'library_id' => 'required|exists:ad_media,id',
            'type'       => 'nullable|string|max:80',
            'headline'   => 'required|string|max:255',
            'sub_text'   => 'nullable|string|max:255',
            'cta_url'    => 'nullable|url',
            'badge'      => 'nullable|string|max:50',
            'accent'     => 'nullable|string|max:30',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        SquareAd::create([
            'library_id' => $request->library_id,
            'type'       => $request->type,
            'headline'   => $request->headline,
            'sub_text'   => $request->sub_text,
            'cta_url'    => $request->cta_url ?? '#',
            'badge'      => $request->badge,
            'accent'     => $request->accent ?? '#ff0808',
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => true,
        ]);

        return redirect()->route('admin.square-ads.index')
            ->with('success', 'Square ad created successfully!');
    }

    public function edit(SquareAd $squareAd)
    {
        $library = AdMedia::orderBy('name')->get();
        return view('admin.square-ads.edit', compact('squareAd', 'library'));
    }

    public function update(Request $request, SquareAd $squareAd)
    {
        $request->validate([
            'library_id' => 'required|exists:ad_media,id',
            'type'       => 'nullable|string|max:80',
            'headline'   => 'required|string|max:255',
            'sub_text'   => 'nullable|string|max:255',
            'cta_url'    => 'nullable|url',
            'badge'      => 'nullable|string|max:50',
            'accent'     => 'nullable|string|max:30',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $squareAd->update([
            'library_id' => $request->library_id,
            'type'       => $request->type,
            'headline'   => $request->headline,
            'sub_text'   => $request->sub_text,
            'cta_url'    => $request->cta_url ?? '#',
            'badge'      => $request->badge,
            'accent'     => $request->accent ?? '#ff0808',
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.square-ads.index')
            ->with('success', 'Square ad updated!');
    }

    public function destroy(SquareAd $squareAd)
    {
        $squareAd->delete();
        return back()->with('success', 'Square ad deleted.');
    }

    public function toggleStatus(SquareAd $squareAd)
    {
        $squareAd->update(['is_active' => !$squareAd->is_active]);
        return back()->with('success', 'Status updated.');
    }
}
