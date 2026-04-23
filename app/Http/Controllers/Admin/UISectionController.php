<?php

// ============================================================
// FILE LOCATION: app/Http/Controllers/Admin/UISectionController.php
// ============================================================

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UISection;
use Illuminate\Http\Request;

class UISectionController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────
    public function index()
    {
        $sections = UISection::ordered()->get()->keyBy('section_key');

        // Seed any missing sections so they always appear in the UI
        foreach (UISection::SECTIONS as $key => $def) {
            if (!$sections->has($key)) {
                $new = UISection::create([
                    'name'         => $def['name'],
                    'section_key'  => $key,
                    'allow_manual' => $def['allow_manual'],
                    'sort_order'   => array_search($key, array_keys(UISection::SECTIONS)),
                ]);
                $sections->put($key, $new);
            }
        }

        $sections = UISection::ordered()->get();

        return view('admin.ui-sections.index', compact('sections'));
    }

    // ─────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────
    public function edit(UISection $uiSection)
    {
        return view('admin.ui-sections.edit', compact('uiSection'));
    }

    // ─────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────
    public function update(Request $request, UISection $uiSection)
    {
        $validated = $request->validate([
            'is_active'    => 'nullable|boolean',
            'animation'    => 'required|in:none,slide,fade,flip',
            'number_items' => 'required|integer|min:1|max:8',
            'manual_items' => 'nullable|array',
            'manual_items.*' => 'integer',
        ]);

        $uiSection->is_active    = $request->boolean('is_active');
        $uiSection->number_items = $validated['number_items'];
        $uiSection->manual_items = $validated['manual_items'] ?? null;
        $uiSection->setAnimationMode($validated['animation']);
        $uiSection->save();

        return redirect()->route('admin.ui-sections.index')
                        ->with('success', '"' . $uiSection->name . '" updated successfully.');
    }

    // ─────────────────────────────────────────────────────────
    // TOGGLE ACTIVE (quick toggle from index)
public function toggleActive(UISection $uiSection)
    {
        $uiSection->update(['is_active' => !$uiSection->is_active]);

        $status = $uiSection->fresh()->is_active ? 'visible' : 'hidden';

        return back()->with('success', '"' . $uiSection->name . '" is now ' . $status . '.');
    }
    // ─────────────────────────────────────────────────────────
    // REORDER (drag & drop sort order via AJAX)
    // ─────────────────────────────────────────────────────────
    public function reorder(Request $request)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:ui_sections,id',
        ]);

        foreach ($request->order as $position => $id) {
            UISection::where('id', $id)->update(['sort_order' => $position]);
        }

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────
    // ADD MANUAL ITEM (AJAX)
    // ─────────────────────────────────────────────────────────
    public function addManualItem(Request $request, UISection $uiSection)
    {
        if (!$uiSection->allow_manual) {
            return response()->json(['error' => 'This section does not support manual items.'], 422);
        }

        $request->validate([
            'item_id'   => 'required|integer',
            'item_type' => 'required|string|in:product,supplier,offer,deal',
        ]);

        $items = $uiSection->manual_items ?? [];

        $newItem = [
            'id'   => $request->item_id,
            'type' => $request->item_type,
        ];

        // Avoid duplicates
        $exists = collect($items)->first(fn($i) => $i['id'] == $newItem['id'] && $i['type'] == $newItem['type']);
        if ($exists) {
            return response()->json(['error' => 'Item already added.'], 422);
        }

        if (count($items) >= $uiSection->number_items) {
            return response()->json(['error' => "Maximum {$uiSection->number_items} items allowed."], 422);
        }

        $items[] = $newItem;
        $uiSection->update(['manual_items' => $items]);

        return response()->json(['success' => true, 'items' => $items]);
    }

    // ─────────────────────────────────────────────────────────
    // REMOVE MANUAL ITEM (AJAX)
    // ─────────────────────────────────────────────────────────
    public function removeManualItem(Request $request, UISection $uiSection)
    {
        $request->validate([
            'item_id'   => 'required|integer',
            'item_type' => 'required|string',
        ]);

        $items = collect($uiSection->manual_items ?? [])
            ->reject(fn($i) => $i['id'] == $request->item_id && $i['type'] == $request->item_type)
            ->values()
            ->all();

        $uiSection->update(['manual_items' => $items]);

        return response()->json(['success' => true, 'items' => $items]);
    }
}
