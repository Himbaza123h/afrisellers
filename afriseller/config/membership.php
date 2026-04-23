<?php

return [
    'tiers' => [
        'free' => [
            'name' => 'Free',
            'price' => 0,
            'duration_days' => null,
            'features' => [
                'product_listings' => 5,
                'rfq_responses' => 3,
                'messaging' => false,
                'analytics' => false,
            ],
        ],
        'silver' => [
            'name' => 'Silver',
            'price' => 29.99,
            'duration_days' => 30,
            'features' => [
                'product_listings' => 50,
                'rfq_responses' => 20,
                'messaging' => true,
                'analytics' => 'basic',
            ],
        ],
        'gold' => [
            'name' => 'Gold',
            'price' => 99.99,
            'duration_days' => 30,
            'features' => [
                'product_listings' => 200,
                'rfq_responses' => 'unlimited',
                'messaging' => true,
                'analytics' => 'advanced',
                'featured_listings' => 5,
            ],
        ],
        'platinum' => [
            'name' => 'Platinum',
            'price' => 249.99,
            'duration_days' => 30,
            'features' => [
                'product_listings' => 1000,
                'rfq_responses' => 'unlimited',
                'messaging' => true,
                'analytics' => 'advanced',
                'featured_listings' => 20,
                'priority_support' => true,
            ],
        ],
        'diamond' => [
            'name' => 'Diamond',
            'price' => 499.99,
            'duration_days' => 30,
            'features' => [
                'product_listings' => 'unlimited',
                'rfq_responses' => 'unlimited',
                'messaging' => true,
                'analytics' => 'premium',
                'featured_listings' => 'unlimited',
                'priority_support' => true,
                'dedicated_manager' => true,
            ],
        ],
    ],
];
