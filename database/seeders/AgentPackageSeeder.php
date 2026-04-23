<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgentPackage;
use Illuminate\Support\Str;

class AgentPackageSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Perfect for getting started as an agent',
                'price' => 0.00,
                'billing_cycle' => 'monthly',
                'is_active' => true,
                'is_featured' => false,
                'max_referrals' => 5,
                'allow_rfqs' => false,
                'priority_support' => false,
                'advanced_analytics' => false,
                'commission_boost' => false,
                'commission_rate' => 3.00,
                'featured_profile' => false,
                'max_payouts_per_month' => 1,
                'duration_days' => 30,
                'sort_order' => 1,
            ],
            [
                'name' => 'Normal',
                'slug' => 'normal',
                'description' => 'Great for growing agents',
                'price' => 29.99,
                'billing_cycle' => 'monthly',
                'is_active' => true,
                'is_featured' => false,
                'max_referrals' => 15,
                'allow_rfqs' => true,
                'priority_support' => false,
                'advanced_analytics' => false,
                'commission_boost' => false,
                'commission_rate' => 5.00,
                'featured_profile' => false,
                'max_payouts_per_month' => 2,
                'duration_days' => 30,
                'sort_order' => 2,
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'description' => 'Popular choice for professional agents',
                'price' => 79.99,
                'billing_cycle' => 'monthly',
                'is_active' => true,
                'is_featured' => true,
                'max_referrals' => 50,
                'allow_rfqs' => true,
                'priority_support' => true,
                'advanced_analytics' => true,
                'commission_boost' => true,
                'commission_rate' => 8.00,
                'featured_profile' => true,
                'max_payouts_per_month' => 4,
                'duration_days' => 30,
                'sort_order' => 3,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Ultimate package for power agents',
                'price' => 149.99,
                'billing_cycle' => 'monthly',
                'is_active' => true,
                'is_featured' => false,
                'max_referrals' => 0, // Unlimited
                'allow_rfqs' => true,
                'priority_support' => true,
                'advanced_analytics' => true,
                'commission_boost' => true,
                'commission_rate' => 10.00,
                'featured_profile' => true,
                'max_payouts_per_month' => 0, // Unlimited
                'duration_days' => 30,
                'sort_order' => 4,
            ],
        ];

        foreach ($packages as $packageData) {
            AgentPackage::create($packageData);
        }
    }
}
