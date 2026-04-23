<?php

namespace App\Observers;

use App\Models\Tradeshow;
use App\Models\GlobalSearchIndex;

class TradeshowObserver
{
    public function created(Tradeshow $tradeshow): void
    {
        $this->syncToSearchIndex($tradeshow);
    }

    public function updated(Tradeshow $tradeshow): void
    {
        $this->syncToSearchIndex($tradeshow);
    }

    public function deleted(Tradeshow $tradeshow): void
    {
        GlobalSearchIndex::where('searchable_type', Tradeshow::class)
            ->where('searchable_id', $tradeshow->id)
            ->delete();
    }

    private function syncToSearchIndex(Tradeshow $tradeshow): void
    {
        if ($tradeshow->status !== 'published' || $tradeshow->deleted_at !== null) {
            GlobalSearchIndex::where('searchable_type', Tradeshow::class)
                ->where('searchable_id', $tradeshow->id)
                ->delete();
            return;
        }

        GlobalSearchIndex::updateOrCreate(
            [
                'searchable_type' => Tradeshow::class,
                'searchable_id' => $tradeshow->id,
            ],
            [
                'title' => $tradeshow->name,
                'description' => $tradeshow->description,
                'search_content' => implode(' ', array_filter([
                    $tradeshow->name,
                    $tradeshow->description,
                    $tradeshow->tradeshow_number,
                    $tradeshow->city,
                    $tradeshow->venue_name,
                    $tradeshow->industry,
                    $tradeshow->category,
                ])),
                'url' => '/tradeshows/' . $tradeshow->slug,
                'metadata' => [
                    'city' => $tradeshow->city,
                    'country_id' => $tradeshow->country_id,
                    'industry' => $tradeshow->industry,
                    'start_date' => $tradeshow->start_date,
                    'end_date' => $tradeshow->end_date,
                    'is_featured' => $tradeshow->is_featured,
                    'is_verified' => $tradeshow->is_verified,
                ],
            ]
        );
    }
}
