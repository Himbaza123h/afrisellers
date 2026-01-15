<?php

namespace App\Observers;

use App\Models\Load;
use App\Models\GlobalSearchIndex;

class LoadObserver
{
    public function created(Load $load): void
    {
        $this->syncToSearchIndex($load);
    }

    public function updated(Load $load): void
    {
        $this->syncToSearchIndex($load);
    }

    public function deleted(Load $load): void
    {
        GlobalSearchIndex::where('searchable_type', Load::class)
            ->where('searchable_id', $load->id)
            ->delete();
    }

    private function syncToSearchIndex(Load $load): void
    {
        if (!in_array($load->status, ['pending', 'posted', 'active', 'bidding', 'in_transit']) || $load->deleted_at !== null) {
            GlobalSearchIndex::where('searchable_type', Load::class)
                ->where('searchable_id', $load->id)
                ->delete();
            return;
        }

        GlobalSearchIndex::updateOrCreate(
            [
                'searchable_type' => Load::class,
                'searchable_id' => $load->id,
            ],
            [
                'title' => 'Load ' . $load->load_number . ' - ' . $load->cargo_type,
                'description' => ($load->origin_city ?? '') . ' to ' . ($load->destination_city ?? '') . ' - ' . ($load->cargo_description ?? ''),
                'search_content' => implode(' ', array_filter([
                    $load->load_number,
                    $load->cargo_type,
                    $load->cargo_description,
                    $load->origin_city,
                    $load->origin_state,
                    $load->destination_city,
                    $load->destination_state,
                    $load->packaging_type,
                ])),
                'url' => '/loadboard/loads/' . $load->load_number,
                'metadata' => [
                    'origin_city' => $load->origin_city,
                    'destination_city' => $load->destination_city,
                    'cargo_type' => $load->cargo_type,
                    'weight' => $load->weight,
                    'status' => $load->status,
                    'budget' => $load->budget,
                ],
            ]
        );
    }
}
