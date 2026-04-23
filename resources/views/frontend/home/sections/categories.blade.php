<section class="py-6 md:py-8 bg-white">
    <div class="container px-4 mx-auto">
        <div class="flex justify-between items-center mb-3 md:mb-4">
            <h2 class="text-base md:text-lg lg:text-xl font-bold text-gray-900">{{ __('messages.shop_by_category') }}</h2>
            <div class="flex gap-1.5">
                <button id="category-prev" class="flex justify-center items-center w-6 h-6 md:w-8 md:h-8 text-gray-700 bg-gray-100 rounded-full transition-all hover:bg-blue-600 hover:text-white">
                    <i class="fas fa-chevron-left text-[10px] md:text-xs"></i>
                </button>
                <button id="category-next" class="flex justify-center items-center w-6 h-6 md:w-8 md:h-8 text-gray-700 bg-gray-100 rounded-full transition-all hover:bg-blue-600 hover:text-white">
                    <i class="fas fa-chevron-right text-[10px] md:text-xs"></i>
                </button>
            </div>
        </div>

        <div class="overflow-hidden relative">
            <div id="category-slider" class="flex gap-2 md:gap-3 transition-transform duration-500 ease-out">
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
                       class="category-slide flex-shrink-0 flex flex-col items-center bg-white hover:shadow-lg rounded-lg md:rounded-xl p-2 md:p-3 text-center transition-all group border border-transparent hover:border-blue-400">
                        <!-- Circular Image Container -->
                        <div class="w-14 h-14 md:w-16 md:h-16 rounded-full {{ $circleColor }} flex items-center justify-center mb-1.5 md:mb-2 transition-transform group-hover:scale-105 overflow-hidden">
                            @if($category->image)
                                <img src="{{ $category->image }}"
                                     alt="{{ $category->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <span class="text-xl md:text-2xl">{{ $icon }}</span>
                            @endif
                        </div>
                        <!-- Category Name -->
                        <div class="text-[9px] md:text-[10px] font-bold text-gray-900 transition-colors group-hover:text-blue-600 leading-tight">
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
        <div class="flex gap-2 justify-center mt-3 md:mt-4">
            <div id="category-dots" class="flex gap-1"></div>
        </div>
    </div>
</section>

<style>
    .category-slide {
        width: calc((100% - 1rem) / 6);
        min-width: 100px;
    }

    @media (max-width: 1280px) {
        .category-slide {
            width: calc((100% - 0.75rem) / 5);
            min-width: 105px;
        }
    }

    @media (max-width: 1024px) {
        .category-slide {
            width: calc((100% - 0.5rem) / 4);
            min-width: 110px;
        }
    }

    @media (max-width: 768px) {
        .category-slide {
            width: calc((100% - 0.5rem) / 3);
            min-width: 95px;
        }
    }

    @media (max-width: 640px) {
        .category-slide {
            width: calc((100% - 0.5rem) / 2.5);
            min-width: 90px;
        }
    }

    @media (max-width: 480px) {
        .category-slide {
            width: calc((100% - 0.5rem) / 2);
            min-width: 100px;
        }
    }
</style>
