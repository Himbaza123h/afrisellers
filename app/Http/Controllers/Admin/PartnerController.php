<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $query = Partner::withTrashed()->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') $query->where('is_active', true)->whereNull('deleted_at');
            elseif ($request->status === 'inactive') $query->where('is_active', false)->whereNull('deleted_at');
            elseif ($request->status === 'deleted') $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
        }

        $partners = $query->orderBy('sort_order')->paginate(15)->withQueryString();

        $stats = [
            'total'    => Partner::count(),
            'active'   => Partner::where('is_active', true)->count(),
            'inactive' => Partner::where('is_active', false)->count(),
        ];

        return view('admin.partners.index', compact('partners', 'stats'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'logo'         => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
            'website_url'  => 'nullable|url|max:500',
            'industry'     => 'nullable|string|max:255',
            'partner_type' => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('partners', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $partner = Partner::create($validated);

        \App\Models\Notification::create([
            'title'     => 'New Partner Added',
            'content'   => $partner->name . ' has been added as a partner on AfriSellers.',
            'link_url'  => '/admin/partners/' . $partner->id,
            'user_id'   => auth()->id(),
            'vendor_id' => null,
            'country_id'=> null,
            'is_read'   => false,
        ]);

        return redirect()->route('admin.partners.index')

            ->with('success', 'Partner created successfully.');
    }

    public function show(Partner $partner)
    {
        return view('admin.partners.show', compact('partner'));
    }

    public function edit(Partner $partner)
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'logo'         => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
            'website_url'  => 'nullable|url|max:500',
            'industry'     => 'nullable|string|max:255',
            'partner_type' => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            if ($partner->logo && !str_starts_with($partner->logo, 'http')) {
                Storage::disk('public')->delete($partner->logo);
            }
            $validated['logo'] = $request->file('logo')->store('partners', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

$partner->update($validated);

        \App\Models\Notification::create([
            'title'     => 'Partner Updated',
            'content'   => $partner->name . ' partner profile has been updated.',
            'link_url'  => '/admin/partners/' . $partner->id,
            'user_id'   => auth()->id(),
            'vendor_id' => null,
            'country_id'=> null,
            'is_read'   => false,
        ]);

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner updated successfully.');
    }

public function destroy(Partner $partner)
    {
        $partnerName = $partner->name;
        $partner->delete();

        \App\Models\Notification::create([
            'title'     => 'Partner Removed',
            'content'   => $partnerName . ' has been removed from AfriSellers partners.',
            'link_url'  => '/admin/partners',
            'user_id'   => auth()->id(),
            'vendor_id' => null,
            'country_id'=> null,
            'is_read'   => false,
        ]);

        return back()->with('success', 'Partner deleted.');
    }

public function toggleStatus(Partner $partner)
    {
        $partner->update(['is_active' => !$partner->is_active]);

        \App\Models\Notification::create([
            'title'     => 'Partner Status Changed',
            'content'   => $partner->name . ' has been ' . ($partner->is_active ? 'activated' : 'deactivated') . ' as a partner.',
            'link_url'  => '/admin/partners/' . $partner->id,
            'user_id'   => auth()->id(),
            'vendor_id' => null,
            'country_id'=> null,
            'is_read'   => false,
        ]);

        return back()->with('success', 'Status updated.');
    }

    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array']);
        foreach ($request->order as $index => $id) {
            Partner::where('id', $id)->update(['sort_order' => $index]);
        }
        return response()->json(['success' => true]);
    }
}
