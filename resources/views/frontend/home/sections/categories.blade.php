<section class="py-6 md:py-8 bg-white">
    <div class="container px-4 mx-auto">
        <div class="flex justify-between items-center mb-4 md:mb-6">
            <h2 class="text-lg md:text-xl font-bold text-gray-900">{{ __('messages.shop_by_category') }}</h2>
            <div class="flex gap-2">
                <button id="category-prev" class="flex justify-center items-center w-8 h-8 md:w-10 md:h-10 text-gray-700 bg-gray-100 rounded-full transition-all hover:bg-blue-600 hover:text-white">
                    <i class="fas fa-chevron-left text-xs md:text-sm"></i>
                </button>
                <button id="category-next" class="flex justify-center items-center w-8 h-8 md:w-10 md:h-10 text-gray-700 bg-gray-100 rounded-full transition-all hover:bg-blue-600 hover:text-white">
                    <i class="fas fa-chevron-right text-xs md:text-sm"></i>
                </button>
            </div>
        </div>

        <div class="overflow-hidden relative">
            <div id="category-slider" class="flex gap-3 md:gap-4 transition-transform duration-500 ease-out">
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

                    // Color gradient classes for circular backgrounds
                    $circleColors = [
                        'bg-orange-100',
                        'bg-amber-100',
                        'bg-yellow-100',
                        'bg-blue-100',
                        'bg-gray-100',
                        'bg-purple-100',
                        'bg-pink-100',
                        'bg-green-100',
                        'bg-teal-100',
                        'bg-indigo-100',
                        'bg-red-100',
                        'bg-cyan-100',
                        'bg-lime-100',
                        'bg-violet-100',
                        'bg-rose-100',
                        'bg-slate-100',
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
                        $circleColor = $circleColors[$index % count($circleColors)];
                        $productCount = number_format($category->products_count, 0);
                    @endphp
                    <a href="{{ route('products.search', ['type' => 'category', 'slug' => \Illuminate\Support\Str::slug($category->name)]) }}"
                       class="category-slide flex-shrink-0 flex flex-col items-center bg-white hover:shadow-lg rounded-xl p-4 md:p-5 text-center transition-all group border border-gray-200 hover:border-blue-400">
                        <!-- Circular Image Container (matching the uploaded image style) -->
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full {{ $circleColor }} flex items-center justify-center mb-3 transition-transform duration-300 group-hover:scale-105 overflow-hidden">
                            @if($category->image)
                                <img src="{{ $category->image }}"
                                     alt="{{ $category->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <span class="text-3xl md:text-4xl">{{ $icon }}</span>
                            @endif
                        </div>
                        <!-- Category Name -->
                        <div class="text-xs md:text-sm font-bold text-gray-900 transition-colors group-hover:text-blue-600 leading-tight">
                            {{ $category->name }}
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-12 text-center">
                        <p class="text-gray-500 text-sm">No categories available yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Progress Dots -->
        <div class="flex gap-2 justify-center mt-4 md:mt-6">
            <div id="category-dots" class="flex gap-1.5"></div>
        </div>
    </div>
</section>

<style>
    .category-slide {
        width: calc((100% - 1.5rem) / 6);
        min-width: 140px;
    }

    @media (max-width: 1280px) {
        .category-slide {
            width: calc((100% - 1.25rem) / 5);
            min-width: 130px;
        }
    }

    @media (max-width: 1024px) {
        .category-slide {
            width: calc((100% - 1rem) / 4);
            min-width: 140px;
        }
    }

    @media (max-width: 768px) {
        .category-slide {
            width: calc((100% - 0.75rem) / 3);
            min-width: 120px;
        }
    }

    @media (max-width: 640px) {
        .category-slide {
            width: calc((100% - 0.75rem) / 2.5);
            min-width: 110px;
        }
    }

    @media (max-width: 480px) {
        .category-slide {
            width: calc((100% - 0.5rem) / 2);
            min-width: 140px;
        }
    }
</style>
