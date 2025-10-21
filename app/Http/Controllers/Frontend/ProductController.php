<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function search($type, $slug)
    {
        // Mock data for demonstration
        $products = $this->getMockProducts();
        $totalProducts = count($products);

        return view('frontend.products.index', [
            'type' => $type,
            'slug' => $slug,
            'products' => $products,
            'totalProducts' => $totalProducts,
            'searchQuery' => ucwords(str_replace('-', ' ', $slug))
        ]);
    }

    private function getMockProducts()
    {
        return [
            [
                'id' => 1,
                'name' => 'Summer New Men\'s Short-sleeved African Ethnic Style T-shirt',
                'image' => 'https://images.pexels.com/photos/8532616/pexels-photo-8532616.jpeg',
                'price' => 'RF 9,881-12,587',
                'min_order' => '2 pieces',
                'supplier' => 'Zhuji Hanshang Trade Co., Ltd.',
                'rating' => '4.8',
                'reviews' => '4',
                'years' => '2',
                'country' => 'CN',
                'reorder_rate' => '20%',
                'badge' => 'Reorder rate 20%'
            ],
            [
                'id' => 2,
                'name' => 'M-4XL New African Styles Street Long Sleeved Men\'s Top+Pants Set',
                'image' => 'https://images.pexels.com/photos/5704852/pexels-photo-5704852.jpeg',
                'price' => 'RF 20,226',
                'min_order' => '1 set',
                'supplier' => 'Shenzhen Yannisfashion Commerce Co., Ltd.',
                'rating' => '3.8',
                'reviews' => '685',
                'years' => '9',
                'country' => 'CN',
                'sold' => '10 sold',
                'delivery' => 'Est. delivery by 25 Nov'
            ],
            [
                'id' => 3,
                'name' => 'Wholesale Luxury Designer Silk Printed Robes Ethnic Style African Loose Women Polyester',
                'image' => 'https://images.pexels.com/photos/5704849/pexels-photo-5704849.jpeg',
                'price' => 'RF 6,839',
                'original_price' => 'RF 7,276',
                'discount' => '6% off',
                'min_order' => '12 pieces',
                'supplier' => 'Yiwu Yadoice Garment Co., Ltd.',
                'rating' => '4.7',
                'reviews' => '99',
                'years' => '7',
                'country' => 'CN',
                'sold' => '285 sold',
                'badge' => 'Lower priced than similar'
            ],
            [
                'id' => 4,
                'name' => 'Men\'s New Fashion 2-Piece Casual Dashiki Polyester Short Sleeve African Clothing',
                'image' => 'https://images.pexels.com/photos/5704850/pexels-photo-5704850.jpeg',
                'price' => 'RF 25,465',
                'min_order' => '2 pieces',
                'supplier' => 'Dongguan City Ziyang Apparel Co., Ltd.',
                'rating' => '4.1',
                'reviews' => '8',
                'years' => '5',
                'country' => 'CN',
                'sold' => '4 sold',
                'delivery' => 'Est. delivery by 19 Nov'
            ],
            [
                'id' => 5,
                'name' => 'Traditional African Print Ankara Dashiki Shirt',
                'image' => 'https://images.pexels.com/photos/5704847/pexels-photo-5704847.jpeg',
                'price' => 'RF 15,500',
                'min_order' => '5 pieces',
                'supplier' => 'Lagos Fashion Hub Ltd.',
                'rating' => '4.9',
                'reviews' => '156',
                'years' => '6',
                'country' => 'NG',
                'sold' => '520 sold',
                'badge' => 'Best Seller'
            ],
            [
                'id' => 6,
                'name' => 'African Wax Print Fabric Kaftan Dress',
                'image' => 'https://images.pexels.com/photos/5704851/pexels-photo-5704851.jpeg',
                'price' => 'RF 18,900',
                'min_order' => '3 pieces',
                'supplier' => 'Accra Textiles Co.',
                'rating' => '4.6',
                'reviews' => '89',
                'years' => '8',
                'country' => 'GH',
                'sold' => '167 sold'
            ],
            [
                'id' => 7,
                'name' => 'Men\'s African Embroidered Tunic Set',
                'image' => 'https://images.pexels.com/photos/8532635/pexels-photo-8532635.jpeg',
                'price' => 'RF 22,300',
                'min_order' => '2 pieces',
                'supplier' => 'Nairobi Fashion House',
                'rating' => '4.5',
                'reviews' => '45',
                'years' => '4',
                'country' => 'KE',
                'sold' => '78 sold',
                'delivery' => 'Est. delivery by 22 Nov'
            ],
            [
                'id' => 8,
                'name' => 'Women\'s Traditional Kente Cloth Dress',
                'image' => 'https://images.pexels.com/photos/5704848/pexels-photo-5704848.jpeg',
                'price' => 'RF 28,750',
                'min_order' => '1 piece',
                'supplier' => 'Kumasi Heritage Textiles',
                'rating' => '4.8',
                'reviews' => '234',
                'years' => '12',
                'country' => 'GH',
                'sold' => '890 sold',
                'badge' => 'Premium Quality'
            ]
        ];
    }
}
