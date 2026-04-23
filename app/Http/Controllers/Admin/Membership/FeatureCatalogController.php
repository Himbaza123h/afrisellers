<?php

namespace App\Http\Controllers\Admin\Membership;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeatureCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Feature::query();

        $searchTerm = trim((string) $request->input('search', ''));
        if ($searchTerm !== '') {
            $like = '%'.addcslashes($searchTerm, '%_\\').'%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('feature_key', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        if ($request->filled('status') && in_array($request->input('status'), ['active', 'inactive'], true)) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('supported') && $request->input('supported') !== null && $request->input('supported') !== '') {
            $v = $request->input('supported');
            if ($v === '1' || $v === '0') {
                $query->where('is_supported', $v === '1');
            }
        }

        if ($request->filled('value_type') && in_array($request->input('value_type'), Feature::VALUE_TYPES, true)) {
            $query->where('value_type', $request->input('value_type'));
        }

        $features = $query->orderBy('name')->paginate(20)->withQueryString();

        $hasFilters = $searchTerm !== ''
            || $request->filled('status')
            || $request->filled('supported')
            || $request->filled('value_type');

        return view('admin.membership.feature-catalog.index', compact('features', 'hasFilters'));
    }

    public function create()
    {
        return view('admin.membership.feature-catalog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:features,slug',
            'feature_key' => 'required|string|max:255|unique:features,feature_key',
            'value_type' => ['required', 'string', Rule::in(Feature::VALUE_TYPES)],
            'status' => 'required|string|in:active,inactive',
            'is_supported' => 'boolean',
        ]);
        $validated['is_supported'] = $request->boolean('is_supported');

        Feature::create($validated);

        return redirect()->route('admin.memberships.feature-catalog.index')
            ->with('success', 'Feature definition created.');
    }

    public function edit(Feature $feature)
    {
        return view('admin.membership.feature-catalog.edit', compact('feature'));
    }

    public function update(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => ['required', 'string', 'max:255', Rule::unique('features', 'slug')->ignore($feature->id)],
            'feature_key' => ['required', 'string', 'max:255', Rule::unique('features', 'feature_key')->ignore($feature->id)],
            'value_type' => ['required', 'string', Rule::in(Feature::VALUE_TYPES)],
            'status' => 'required|string|in:active,inactive',
            'is_supported' => 'boolean',
        ]);
        $validated['is_supported'] = $request->boolean('is_supported');

        if ($validated['status'] === 'inactive' && $feature->status === 'active' && $feature->planFeatures()->exists()) {
            return back()
                ->withErrors(['status' => $this->cannotDeactivateWithPlansMessage()])
                ->withInput();
        }

        if (! $validated['is_supported'] && $feature->is_supported && $feature->planFeatures()->exists()) {
            return back()
                ->withErrors(['is_supported' => $this->cannotUnsupportedWithPlansMessage()])
                ->withInput();
        }

        $feature->update($validated);

        return redirect()->route('admin.memberships.feature-catalog.index')
            ->with('success', 'Feature definition updated.');
    }

    public function destroy(Feature $feature)
    {
        if ($feature->planFeatures()->exists()) {
            return back()->with('error', 'Cannot delete a feature that is assigned to one or more plans.');
        }

        $feature->delete();

        return redirect()->route('admin.memberships.feature-catalog.index')
            ->with('success', 'Feature definition deleted.');
    }

    public function toggleStatus(Request $request, Feature $feature)
    {
        $nextStatus = $feature->status === 'active' ? 'inactive' : 'active';

        if ($nextStatus === 'inactive' && $feature->planFeatures()->exists()) {
            $msg = $this->cannotDeactivateWithPlansMessage();

            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        $feature->update(['status' => $nextStatus]);
        $message = 'Status updated.';

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'status' => $feature->status,
            ]);
        }

        return back()->with('success', $message);
    }

    public function toggleSupported(Request $request, Feature $feature)
    {
        $nextSupported = ! $feature->is_supported;

        if (! $nextSupported && $feature->planFeatures()->exists()) {
            $msg = $this->cannotUnsupportedWithPlansMessage();

            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        $feature->update(['is_supported' => $nextSupported]);
        $message = 'Supported updated.';

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'is_supported' => $feature->is_supported,
            ]);
        }

        return back()->with('success', $message);
    }

    private function cannotDeactivateWithPlansMessage(): string
    {
        return 'This feature is still assigned to one or more membership plans. Open each plan (Memberships → Plans → Manage features), remove this feature, then set it to Inactive here.';
    }

    private function cannotUnsupportedWithPlansMessage(): string
    {
        return 'This feature is still assigned to one or more membership plans. Remove it from every plan first, then you can mark it as not supported.';
    }
}
