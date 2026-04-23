<?php

namespace App\Observers;

use App\Models\Car;
use App\Models\GlobalSearchIndex;

class CarObserver
{
    public function created(Car $car): void
    {
        $this->syncToSearchIndex($car);
    }

    public function updated(Car $car): void
    {
        $this->syncToSearchIndex($car);
    }

    public function deleted(Car $car): void
    {
        GlobalSearchIndex::where('searchable_type', Car::class)
            ->where('searchable_id', $car->id)
            ->delete();
    }

    private function syncToSearchIndex(Car $car): void
    {
        if ($car->availability_status !== 'available' || $car->deleted_at !== null) {
            GlobalSearchIndex::where('searchable_type', Car::class)
                ->where('searchable_id', $car->id)
                ->delete();
            return;
        }

        GlobalSearchIndex::updateOrCreate(
            [
                'searchable_type' => Car::class,
                'searchable_id' => $car->id,
            ],
            [
                'title' => ($car->year ?? '') . ' ' . ($car->make ?? '') . ' ' . ($car->model ?? '') . ' - ' . $car->listing_number,
                'description' => ($car->from_city ?? '') . ' to ' . ($car->to_city ?? 'Flexible') . ' - ' . ($car->vehicle_type ?? '') . ', ' . ($car->transmission ?? ''),
                'search_content' => implode(' ', array_filter([
                    $car->listing_number,
                    $car->make,
                    $car->model,
                    $car->year,
                    $car->vehicle_type,
                    $car->condition,
                    $car->transmission,
                    $car->fuel_type,
                    $car->color,
                    $car->from_city,
                    $car->to_city,
                    $car->description,
                    $car->vin,
                ])),
                'url' => '/loadboard/cars/' . $car->listing_number,
                'metadata' => [
                    'make' => $car->make,
                    'model' => $car->model,
                    'year' => $car->year,
                    'vehicle_type' => $car->vehicle_type,
                    'price' => $car->price,
                    'is_featured' => $car->is_featured,
                    'is_verified' => $car->is_verified,
                ],
            ]
        );
    }
}
