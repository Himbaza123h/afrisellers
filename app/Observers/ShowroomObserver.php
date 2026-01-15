<?php

namespace App\Observers;

use App\Models\Showroom;
use App\Models\GlobalSearchIndex;

class ShowroomObserver
{
    public function created(Showroom $showroom): void
    {
        $this->syncToSearchIndex($showroom);
    }

    public function updated(Showroom $showroom): void
    {
        $this->syncToSearchIndex($showroom);
    }

    public function deleted(Showroom $showroom): void
    {
        GlobalSearchIndex::where('searchable_type', Showroom::class)
            ->where('searchable_id', $showroom->id)
            ->delete();
    }

    private function syncToSearchIndex(Showroom $showroom): void
    {
        if ($showroom->status !== 'active' || $showroom->deleted_at !== null) {
            GlobalSearchIndex::where('searchable_type', Showroom::class)
                ->where('searchable_id', $showroom->id)
                ->delete();
            return;
        }

        GlobalSearchIndex::updateOrCreate(
            [
                'searchable_type' => Showroom::class,
                'searchable_id' => $showroom->id,
            ],
            [
                'title' => $showroom->name,
                'description' => $showroom->description,
                'search_content' => implode(' ', array_filter([
                    $showroom->name,
                    $showroom->description,
                    $showroom->slug,
                    $showroom->city,
                    $showroom->address,
                    $showroom->business_type,
                    $showroom->industry,
                    $showroom->contact_person,
                ])),
                'url' => '/showrooms/' . $showroom->slug,
                'metadata' => [
                    'city' => $showroom->city,
                    'country_id' => $showroom->country_id,
                    'business_type' => $showroom->business_type,
                    'industry' => $showroom->industry,
                    'is_featured' => $showroom->is_featured,
                    'is_verified' => $showroom->is_verified,
                ],
            ]
        );
    }
}
