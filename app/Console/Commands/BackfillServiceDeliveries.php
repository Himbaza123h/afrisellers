<?php

namespace App\Console\Commands;

use App\Models\ServiceDelivery;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;

class BackfillServiceDeliveries extends Command
{
    protected $signature   = 'services:backfill';
    protected $description = 'Create missing service_deliveries rows for existing active subscriptions';

    public function handle(): void
    {
        $manualKeys = ServiceDelivery::manualFeatureKeys();

        $subscriptions = Subscription::where('status', 'active')
            ->with(['plan.features'])
            ->get();

        $created = 0;
        $skipped = 0;

        foreach ($subscriptions as $sub) {
            if (!$sub->plan) continue;

            foreach ($sub->plan->features as $feature) {
                if (
                    !isset($manualKeys[$feature->feature_key]) ||
                    strtolower($feature->feature_value) !== 'true'
                ) continue;

                // Skip if already exists
                $exists = ServiceDelivery::where('user_id', $sub->seller_id)
                    ->where('subscription_id', $sub->id)
                    ->where('feature_key', $feature->feature_key)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                ServiceDelivery::create([
                    'user_id'         => $sub->seller_id,
                    'plan_id'         => $sub->plan_id,
                    'subscription_id' => $sub->id,
                    'feature_key'     => $feature->feature_key,
                    'service_name'    => $manualKeys[$feature->feature_key],
                    'status'          => 'pending',
                ]);

                $created++;
                $this->line("  ✓ Created: {$manualKeys[$feature->feature_key]} for user {$sub->seller_id}");
            }
        }

        $this->info("Done — {$created} created, {$skipped} skipped (already existed).");
    }
}
