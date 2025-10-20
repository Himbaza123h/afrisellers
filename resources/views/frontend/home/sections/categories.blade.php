<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Shop by Category</h2>
            <div class="flex gap-2">
                <button id="category-prev" class="bg-gray-100 hover:bg-blue-600 hover:text-white text-gray-700 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button id="category-next" class="bg-gray-100 hover:bg-blue-600 hover:text-white text-gray-700 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <div class="relative overflow-hidden">
            <div id="category-slider" class="flex transition-transform duration-500 ease-out gap-4">

                @foreach([
                    ['icon' => '🌾', 'name' => 'Agriculture', 'count' => '5,234', 'color' => 'from-green-50 to-green-100'],
                    ['icon' => '💻', 'name' => 'Electronics', 'count' => '8,432', 'color' => 'from-blue-50 to-blue-100'],
                    ['icon' => '👔', 'name' => 'Fashion', 'count' => '6,789', 'color' => 'from-purple-50 to-purple-100'],
                    ['icon' => '🏭', 'name' => 'Industrial', 'count' => '4,567', 'color' => 'from-gray-50 to-gray-100'],
                    ['icon' => '🏗️', 'name' => 'Construction', 'count' => '3,456', 'color' => 'from-orange-50 to-orange-100'],
                    ['icon' => '🏥', 'name' => 'Healthcare', 'count' => '2,345', 'color' => 'from-red-50 to-red-100'],
                    ['icon' => '🍎', 'name' => 'Food & Beverage', 'count' => '7,890', 'color' => 'from-yellow-50 to-yellow-100'],
                    ['icon' => '🚗', 'name' => 'Automotive', 'count' => '4,123', 'color' => 'from-indigo-50 to-indigo-100'],
                    ['icon' => '🏡', 'name' => 'Home & Garden', 'count' => '5,678', 'color' => 'from-teal-50 to-teal-100'],
                    ['icon' => '💄', 'name' => 'Beauty & Personal Care', 'count' => '3,890', 'color' => 'from-pink-50 to-pink-100'],
                    ['icon' => '📚', 'name' => 'Books & Education', 'count' => '2,567', 'color' => 'from-cyan-50 to-cyan-100'],
                    ['icon' => '⚽', 'name' => 'Sports & Outdoors', 'count' => '4,234', 'color' => 'from-lime-50 to-lime-100'],
                    ['icon' => '🎵', 'name' => 'Music & Instruments', 'count' => '1,789', 'color' => 'from-violet-50 to-violet-100'],
                    ['icon' => '🎨', 'name' => 'Arts & Crafts', 'count' => '3,456', 'color' => 'from-rose-50 to-rose-100'],
                    ['icon' => '🐾', 'name' => 'Pet Supplies', 'count' => '2,890', 'color' => 'from-amber-50 to-amber-100'],
                    ['icon' => '🔧', 'name' => 'Tools & Hardware', 'count' => '5,123', 'color' => 'from-slate-50 to-slate-100'],
                ] as $category)
                <a href="#" class="category-slide flex-shrink-0 bg-gradient-to-br {{ $category['color'] }} hover:shadow-xl rounded-xl p-6 text-center transition-all group border-2 border-transparent hover:border-blue-400">
                    <div class="text-5xl mb-4 group-hover:scale-125 transition-transform duration-300">{{ $category['icon'] }}</div>
                    <div class="font-bold text-gray-900 mb-2 text-base group-hover:text-blue-600 transition-colors">{{ $category['name'] }}</div>
                    <div class="text-sm text-gray-600 font-semibold">{{ $category['count'] }} items</div>
                </a>
                @endforeach
            </>
        </div>

        <!-- Progress Dots -->
        <div class="flex justify-center gap-2 mt-8">
            <div id="category-dots" class="flex gap-2"></div>
        </div>
    </div>
</section>

<style>
    .category-slide {
        width: calc((100% - 3rem) / 4);
        min-width: 150px;
    }

    @media (max-width: 1024px) {
        .category-slide {
            width: calc((100% - 2rem) / 3);
        }
    }

    @media (max-width: 768px) {
        .category-slide {
            width: calc((100% - 1rem) / 2);
        }
    }

    @media (max-width: 480px) {
        .category-slide {
            width: calc(100% - 1rem);
        }
    }
</style>



