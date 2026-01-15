<?php

namespace Database\Seeders;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addons = [
            // Homepage Hero Section
            [
                'country_id' => null, // Available for all countries
                'locationX' => 'Homepage',
                'locationY' => 'herosection',
                'price' => 199.99,
            ],

            // Homepage Featured Suppliers
            [
                'country_id' => null,
                'locationX' => 'Homepage',
                'locationY' => 'featuredsuppliers',
                'price' => 149.99,
            ],

            // Homepage Trending Products
            [
                'country_id' => null,
                'locationX' => 'Homepage',
                'locationY' => 'trendingproducts',
                'price' => 129.99,
            ],

            // Homepage Top Banner
            [
                'country_id' => null,
                'locationX' => 'Homepage',
                'locationY' => 'topbanner',
                'price' => 249.99,
            ],

            // Homepage Sidebar
            [
                'country_id' => null,
                'locationX' => 'Homepage',
                'locationY' => 'sidebar',
                'price' => 99.99,
            ],

            // About Page Featured
            [
                'country_id' => null,
                'locationX' => 'About',
                'locationY' => 'featured',
                'price' => 89.99,
            ],

            // Products Page Top Featured
            [
                'country_id' => null,
                'locationX' => 'Products',
                'locationY' => 'topfeatured',
                'price' => 179.99,
            ],

            // Products Page Sidebar
            [
                'country_id' => null,
                'locationX' => 'Products',
                'locationY' => 'sidebar',
                'price' => 79.99,
            ],

            // Suppliers Page Featured
            [
                'country_id' => null,
                'locationX' => 'Suppliers',
                'locationY' => 'featured',
                'price' => 159.99,
            ],

            // Suppliers Page Top Banner
            [
                'country_id' => null,
                'locationX' => 'Suppliers',
                'locationY' => 'topbanner',
                'price' => 219.99,
            ],

            // Marketplace Featured
            [
                'country_id' => null,
                'locationX' => 'Marketplace',
                'locationY' => 'featured',
                'price' => 189.99,
            ],

            // Marketplace Top Spot
            [
                'country_id' => null,
                'locationX' => 'Marketplace',
                'locationY' => 'topspot',
                'price' => 299.99,
            ],

            // Category Page Featured
            [
                'country_id' => null,
                'locationX' => 'Category',
                'locationY' => 'featured',
                'price' => 139.99,
            ],

            // Search Results Premium
            [
                'country_id' => null,
                'locationX' => 'Search',
                'locationY' => 'premium',
                'price' => 169.99,
            ],

            // Footer Sponsors
            [
                'country_id' => null,
                'locationX' => 'Footer',
                'locationY' => 'sponsors',
                'price' => 59.99,
            ],
        ];

        foreach ($addons as $addon) {
            Addon::create($addon);
        }

        $this->command->info('Addons seeded successfully!');
    }
}
