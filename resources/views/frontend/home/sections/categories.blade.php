<section class="py-12 bg-white">
    <div class="container px-4 mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-lg font-bold text-gray-900">{{ __('messages.shop_by_category') }}</h2>
            <div class="flex gap-2">
                <button id="category-prev" class="flex justify-center items-center w-10 h-10 text-gray-700 bg-gray-100 rounded-full transition-all hover:bg-blue-600 hover:text-white">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button id="category-next" class="flex justify-center items-center w-10 h-10 text-gray-700 bg-gray-100 rounded-full transition-all hover:bg-blue-600 hover:text-white">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <div class="overflow-hidden relative">
            <div id="category-slider" class="flex gap-4 transition-transform duration-500 ease-out">
                @php
                    // Icon mapping based on category name keywords
                    $iconMap = [
                        'agriculture' => '🌾',
                        'food' => '🍎',
                        'beverage' => '🥤',
                        'electronics' => '💻',
                        'technology' => '💻',
                        'fashion' => '👔',
                        'clothing' => '👔',
                        'textile' => '👔',
                        'industrial' => '🏭',
                        'machinery' => '🏭',
                        'construction' => '🏗️',
                        'building' => '🏗️',
                        'healthcare' => '🏥',
                        'medical' => '🏥',
                        'automotive' => '🚗',
                        'vehicle' => '🚗',
                        'home' => '🏡',
                        'garden' => '🏡',
                        'furniture' => '🏡',
                        'beauty' => '💄',
                        'personal' => '💄',
                        'care' => '💄',
                        'cosmetic' => '💄',
                        'books' => '📚',
                        'education' => '📚',
                        'sports' => '⚽',
                        'outdoor' => '⚽',
                        'music' => '🎵',
                        'instrument' => '🎵',
                        'arts' => '🎨',
                        'craft' => '🎨',
                        'pet' => '🐾',
                        'animal' => '🐾',
                        'tools' => '🔧',
                        'hardware' => '🔧',
                        'default' => '📦',
                    ];

                    // Color gradient classes
                    $colorClasses = [
                        'from-green-50 to-green-100',
                        'from-blue-50 to-blue-100',
                        'from-purple-50 to-purple-100',
                        'from-gray-50 to-gray-100',
                        'from-orange-50 to-orange-100',
                        'from-red-50 to-red-100',
                        'from-yellow-50 to-yellow-100',
                        'from-indigo-50 to-indigo-100',
                        'from-teal-50 to-teal-100',
                        'from-pink-50 to-pink-100',
                        'from-cyan-50 to-cyan-100',
                        'from-lime-50 to-lime-100',
                        'from-violet-50 to-violet-100',
                        'from-rose-50 to-rose-100',
                        'from-amber-50 to-amber-100',
                        'from-slate-50 to-slate-100',
                    ];

                    // Function to get icon for category
                    function getCategoryIcon($categoryName, $iconMap) {
                        $nameLower = strtolower($categoryName);
                        foreach ($iconMap as $key => $icon) {
                            if ($key !== 'default' && strpos($nameLower, $key) !== false) {
                                return $icon;
                            }
                        }
                        return $iconMap['default'];
                    }
                @endphp

                @forelse($categories as $index => $category)
                    @php
                        $icon = getCategoryIcon($category->name, $iconMap);
                        $colorClass = $colorClasses[$index % count($colorClasses)];
                        $productCount = number_format($category->products_count, 0);
                    @endphp
                    <a href="{{ route('products.search', ['type' => 'category', 'slug' => \Illuminate\Support\Str::slug($category->name)]) }}"
                       class="category-slide flex-shrink-0 bg-gradient-to-br {{ $colorClass }} hover:shadow-xl rounded-xl p-6 text-center transition-all group border-2 border-transparent hover:border-blue-400">
                        <div class="mb-4 text-3xl transition-transform duration-300 group-hover:scale-125">{{ $icon }}</div>
                        <div class="mb-2 text-base font-bold text-gray-900 transition-colors group-hover:text-blue-600">{{ $category->name }}</div>
                        <div class="text-sm font-semibold text-gray-600">{{ $productCount }} {{ __('messages.items') }}</div>
                    </a>
                @empty
                    <div class="col-span-full py-12 text-center">
                        <p class="text-gray-500">No categories available yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Progress Dots -->
        <div class="flex gap-2 justify-center mt-8">
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
