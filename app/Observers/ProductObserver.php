<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\GlobalSearchIndex;

class ProductObserver
{
    public function created(Product $product): void
    {
        $this->syncToSearchIndex($product);
    }

    public function updated(Product $product): void
    {
        $this->syncToSearchIndex($product);
    }

    public function deleted(Product $product): void
    {
        GlobalSearchIndex::where('searchable_type', Product::class)
            ->where('searchable_id', $product->id)
            ->delete();
    }

    private function syncToSearchIndex(Product $product): void
    {
        if ($product->status !== 'active' || $product->deleted_at !== null) {
            GlobalSearchIndex::where('searchable_type', Product::class)
                ->where('searchable_id', $product->id)
                ->delete();
            return;
        }

        GlobalSearchIndex::updateOrCreate(
            [
                'searchable_type' => Product::class,
                'searchable_id' => $product->id,
            ],
            [
                'title' => $product->name,
                'description' => $product->short_description,
                'search_content' => implode(' ', array_filter([
                    $product->name,
                    $product->short_description,
                    $product->description,
                    $product->slug,
                ])),
                'url' => route('products.show', $product->slug),
                'metadata' => [
                    'min_order_quantity' => $product->min_order_quantity,
                    'is_negotiable' => $product->is_negotiable,
                    'category_id' => $product->product_category_id,
                    'country_id' => $product->country_id,
                ],
            ]
        );
    }
}
