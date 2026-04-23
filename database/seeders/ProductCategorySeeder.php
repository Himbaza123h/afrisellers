<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics & Technology',
                'description' => 'Electronic devices, gadgets, computers, mobile phones, and technology products.',
                'status' => 'active',
            ],
            [
                'name' => 'Textiles & Apparel',
                'description' => 'Fabrics, clothing, garments, textiles, and fashion accessories.',
                'status' => 'active',
            ],
            [
                'name' => 'Agriculture & Farming',
                'description' => 'Agricultural products, farming equipment, seeds, fertilizers, and livestock supplies.',
                'status' => 'active',
            ],
            [
                'name' => 'Food & Beverages',
                'description' => 'Food products, beverages, processed foods, and food ingredients.',
                'status' => 'active',
            ],
            [
                'name' => 'Construction Materials',
                'description' => 'Building materials, construction supplies, cement, steel, and hardware.',
                'status' => 'active',
            ],
            [
                'name' => 'Automotive & Transportation',
                'description' => 'Vehicle parts, automotive accessories, transportation equipment, and spare parts.',
                'status' => 'active',
            ],
            [
                'name' => 'Health & Beauty',
                'description' => 'Health products, beauty supplies, cosmetics, personal care items, and pharmaceuticals.',
                'status' => 'active',
            ],
            [
                'name' => 'Home & Furniture',
                'description' => 'Furniture, home decor, household items, and interior design products.',
                'status' => 'active',
            ],
            [
                'name' => 'Industrial Equipment',
                'description' => 'Machinery, industrial tools, manufacturing equipment, and heavy machinery.',
                'status' => 'active',
            ],
            [
                'name' => 'Energy & Power',
                'description' => 'Solar panels, generators, batteries, power equipment, and energy solutions.',
                'status' => 'active',
            ],
            [
                'name' => 'Packaging & Printing',
                'description' => 'Packaging materials, printing services, labels, boxes, and packaging solutions.',
                'status' => 'active',
            ],
            [
                'name' => 'Chemicals & Raw Materials',
                'description' => 'Chemical products, raw materials, industrial chemicals, and chemical compounds.',
                'status' => 'active',
            ],
            [
                'name' => 'Medical & Healthcare',
                'description' => 'Medical equipment, healthcare supplies, hospital supplies, and medical devices.',
                'status' => 'active',
            ],
            [
                'name' => 'Sports & Recreation',
                'description' => 'Sports equipment, recreational products, fitness equipment, and outdoor gear.',
                'status' => 'active',
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Office equipment, stationery, office furniture, and business supplies.',
                'status' => 'active',
            ],
            [
                'name' => 'Plastics & Rubber',
                'description' => 'Plastic products, rubber goods, plastic raw materials, and plastic manufacturing.',
                'status' => 'active',
            ],
            [
                'name' => 'Metals & Minerals',
                'description' => 'Metal products, minerals, metal raw materials, and metal processing.',
                'status' => 'active',
            ],
            [
                'name' => 'Paper & Paper Products',
                'description' => 'Paper products, paper raw materials, paperboard, and paper manufacturing.',
                'status' => 'active',
            ],
            [
                'name' => 'Tools & Hardware',
                'description' => 'Hand tools, power tools, hardware supplies, and tool accessories.',
                'status' => 'active',
            ],
            [
                'name' => 'Telecommunications',
                'description' => 'Telecom equipment, networking devices, communication systems, and IT infrastructure.',
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Product categories seeded successfully!');
    }
}
