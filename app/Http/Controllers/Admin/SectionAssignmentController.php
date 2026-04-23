<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UISection;
use App\Models\Product;
use Illuminate\Http\Request;

class SectionAssignmentController extends Controller
{
    /**
     * Return all sections that support manual items,
     * along with which ones the given product is already in.
     */
    public function getSectionsForProduct(Request $request, Product $product)
    {
        $sections = UISection::ordered()->get()->map(function ($section) use ($product) {
            $manualItems = collect($section->manual_items ?? []);
            $assigned    = $manualItems->contains(fn($i) => (int)$i['id'] === $product->id && $i['type'] === 'product');

            return [
                'id'          => $section->id,
                'name'        => $section->name,
                'section_key' => $section->section_key,
                'is_active'   => $section->is_active,
                'assigned'    => $assigned,
                'count'       => $manualItems->count(),
                'max'         => $section->number_items,
                'full'        => $manualItems->count() >= $section->number_items && !$assigned,
            ];
        });

        return response()->json([
            'product'  => [
                'id'   => $product->id,
                'name' => $product->name,
            ],
            'sections' => $sections,
        ]);
    }

    /**
     * Assign a product to one or more sections (sync).
     * Sends the full desired list of section_ids.
     */
    public function syncProduct(Request $request, Product $product)
    {
        $request->validate([
            'section_ids'   => 'required|array',
            'section_ids.*' => 'integer|exists:ui_sections,id',
        ]);

        $targetIds = collect($request->section_ids)->map(fn($id) => (int)$id);

        // All sections
        $allSections = UISection::all();

        foreach ($allSections as $section) {
            $items    = collect($section->manual_items ?? []);
            $hasIt    = $items->contains(fn($i) => (int)$i['id'] === $product->id && $i['type'] === 'product');
            $wantsIt  = $targetIds->contains($section->id);

            if ($wantsIt && !$hasIt) {
                // Add — respect limit
                if ($items->count() < $section->number_items) {
                    $items->push(['id' => $product->id, 'type' => 'product']);
                    $section->update(['manual_items' => $items->values()->all()]);
                }
            } elseif (!$wantsIt && $hasIt) {
                // Remove
                $updated = $items->reject(fn($i) => (int)$i['id'] === $product->id && $i['type'] === 'product');
                $section->update(['manual_items' => $updated->values()->all()]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Section assignments updated.']);
    }

    /**
     * Quick-toggle: add or remove a single product from a single section.
     */
    public function toggleProduct(Request $request, UISection $uiSection, Product $product)
    {
        $items   = collect($uiSection->manual_items ?? []);
        $hasIt   = $items->contains(fn($i) => (int)$i['id'] === $product->id && $i['type'] === 'product');

        if ($hasIt) {
            $updated = $items->reject(fn($i) => (int)$i['id'] === $product->id && $i['type'] === 'product');
            $uiSection->update(['manual_items' => $updated->values()->all()]);
            $action = 'removed';
        } else {
            if ($items->count() >= $uiSection->number_items) {
                return response()->json(['success' => false, 'message' => "Section is full (max {$uiSection->number_items})."], 422);
            }
            $items->push(['id' => $product->id, 'type' => 'product']);
            $uiSection->update(['manual_items' => $items->values()->all()]);
            $action = 'added';
        }

        return response()->json(['success' => true, 'action' => $action]);
    }

    /**
     * Return all products assigned to a section (for the section edit page).
     */
    public function getSectionProducts(UISection $uiSection)
    {
        $manualItems = collect($uiSection->manual_items ?? [])
            ->where('type', 'product')
            ->pluck('id');

        $products = Product::whereIn('id', $manualItems)
            ->where('status', 'active')
            ->with(['images' => fn($q) => $q->where('is_primary', true)->limit(1)])
            ->get()
            ->map(fn($p) => [
                'id'    => $p->id,
                'name'  => $p->name,
                'image' => $p->images->first()?->image_url,
            ]);

        return response()->json(['products' => $products]);
    }
}
