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

        <div class="overflow-hidden relative" id="category-wrapper" style="cursor:grab;">
            <div id="category-slider" class="flex gap-2 md:gap-3">
                @php
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

                    $circleColors = [
                        'bg-orange-100', 'bg-amber-100', 'bg-yellow-100', 'bg-blue-100',
                        'bg-gray-100', 'bg-purple-100', 'bg-pink-100', 'bg-green-100',
                        'bg-teal-100', 'bg-indigo-100', 'bg-red-100', 'bg-cyan-100',
                        'bg-lime-100', 'bg-violet-100', 'bg-rose-100', 'bg-slate-100',
                    ];

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
                    @endphp
                    <a href="{{ route('products.search', ['type' => 'category', 'slug' => \Illuminate\Support\Str::slug($category->name)]) }}"
                       class="category-slide flex-shrink-0 flex flex-col items-center bg-white hover:shadow-lg rounded-lg md:rounded-xl p-2 md:p-3 text-center transition-all group border border-transparent hover:border-blue-400">
                        <div class="w-14 h-14 md:w-16 md:h-16 rounded-full {{ $circleColor }} flex items-center justify-center mb-1.5 md:mb-2 transition-transform group-hover:scale-105 overflow-hidden">
                            @if($category->image)
                                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-xl md:text-2xl">{{ $icon }}</span>
                            @endif
                        </div>
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
    @media (max-width: 1280px) { .category-slide { width: calc((100% - 0.75rem) / 5); min-width: 105px; } }
    @media (max-width: 1024px) { .category-slide { width: calc((100% - 0.5rem) / 4);  min-width: 110px; } }
    @media (max-width: 768px)  { .category-slide { width: calc((100% - 0.5rem) / 3);  min-width: 95px;  } }
    @media (max-width: 640px)  { .category-slide { width: calc((100% - 0.5rem) / 2.5);min-width: 90px;  } }
    @media (max-width: 480px)  { .category-slide { width: calc((100% - 0.5rem) / 2);  min-width: 100px; } }

    #category-wrapper.dragging { cursor: grabbing; }
    #category-slider { will-change: transform; user-select: none; }
    #category-slider a { pointer-events: auto; }
    #category-wrapper.dragging a { pointer-events: none; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const wrapper  = document.getElementById('category-wrapper');
    const slider   = document.getElementById('category-slider');
    const dotsEl   = document.getElementById('category-dots');
    const btnPrev  = document.getElementById('category-prev');
    const btnNext  = document.getElementById('category-next');
    if (!wrapper || !slider) return;

    const SPEED        = 0.5;   // px per frame auto-scroll
    const FRICTION     = 0.92;  // momentum decay

    let offset       = 0;
    let maxOffset    = 0;
    let velocity     = 0;
    let paused       = false;
    let dragging     = false;
    let dragStartX   = 0;
    let dragStartOff = 0;
    let lastDragX    = 0;
    let lastDragTime = 0;
    let totalDots    = 0;

    function clamp(val, min, max) { return Math.min(Math.max(val, min), max); }

    function getMaxOffset() {
        return Math.max(0, slider.scrollWidth - wrapper.offsetWidth);
    }

    function applyTransform() {
        slider.style.transform = `translateX(${-offset}px)`;
    }

    // ── Dots ──
    function buildDots() {
        if (!dotsEl) return;
        const visibleW = wrapper.offsetWidth;
        totalDots = Math.ceil(slider.scrollWidth / visibleW) || 1;
        dotsEl.innerHTML = '';
        for (let i = 0; i < totalDots; i++) {
            const d = document.createElement('button');
            d.className = 'w-1.5 h-1.5 rounded-full bg-gray-300 transition-all';
            d.addEventListener('click', () => {
                velocity = 0;
                offset   = clamp(i * visibleW, 0, getMaxOffset());
            });
            dotsEl.appendChild(d);
        }
        updateDots();
    }

    function updateDots() {
        if (!dotsEl) return;
        const visibleW = wrapper.offsetWidth || 1;
        const active   = Math.round(offset / visibleW);
        Array.from(dotsEl.children).forEach((d, i) => {
            d.className = i === active
                ? 'w-3 h-1.5 rounded-full bg-blue-600 transition-all'
                : 'w-1.5 h-1.5 rounded-full bg-gray-300 transition-all';
        });
    }

    // ── RAF loop ──
    function loop() {
        maxOffset = getMaxOffset();

        if (!paused && !dragging) {
            if (Math.abs(velocity) > 0.1) {
                offset   += velocity;
                velocity *= FRICTION;
            } else {
                velocity  = 0;
                offset   += SPEED;
            }

            // Bounce back at ends for auto-scroll: loop back to start
            if (offset >= maxOffset + 60) offset = 0;
            if (offset < 0)              offset = 0;
        } else if (!dragging && Math.abs(velocity) > 0.1) {
            offset   += velocity;
            velocity *= FRICTION;
            offset    = clamp(offset, 0, maxOffset);
        }

        applyTransform();
        updateDots();
        requestAnimationFrame(loop);
    }

    // ── Hover pause ──
    wrapper.addEventListener('mouseenter', () => { if (!dragging) paused = true;  });
    wrapper.addEventListener('mouseleave', () => { if (!dragging) paused = false; });

    // ── Mouse drag ──
    wrapper.addEventListener('mousedown', (e) => {
        dragging     = true;
        paused       = true;
        dragStartX   = e.clientX;
        dragStartOff = offset;
        lastDragX    = e.clientX;
        lastDragTime = Date.now();
        velocity     = 0;
        wrapper.classList.add('dragging');
        e.preventDefault();
    });

    document.addEventListener('mousemove', (e) => {
        if (!dragging) return;
        const dx = dragStartX - e.clientX;
        offset   = clamp(dragStartOff + dx, 0, getMaxOffset());

        const now = Date.now();
        const dt  = now - lastDragTime;
        if (dt > 0) velocity = (e.clientX - lastDragX) / dt * -16;
        lastDragX    = e.clientX;
        lastDragTime = now;
    });

    document.addEventListener('mouseup', () => {
        if (!dragging) return;
        dragging = false;
        paused   = false;
        wrapper.classList.remove('dragging');
    });

    // ── Touch drag ──
    wrapper.addEventListener('touchstart', (e) => {
        dragging     = true;
        paused       = true;
        dragStartX   = e.touches[0].clientX;
        dragStartOff = offset;
        lastDragX    = e.touches[0].clientX;
        lastDragTime = Date.now();
        velocity     = 0;
    }, { passive: true });

    wrapper.addEventListener('touchmove', (e) => {
        if (!dragging) return;
        const dx = dragStartX - e.touches[0].clientX;
        offset   = clamp(dragStartOff + dx, 0, getMaxOffset());

        const now = Date.now();
        const dt  = now - lastDragTime;
        if (dt > 0) velocity = (e.touches[0].clientX - lastDragX) / dt * -16;
        lastDragX    = e.touches[0].clientX;
        lastDragTime = now;
    }, { passive: true });

    wrapper.addEventListener('touchend', () => {
        dragging = false;
        paused   = false;
    });

    // ── Prev / Next buttons ──
    if (btnPrev) {
        btnPrev.addEventListener('click', () => {
            velocity = 0;
            offset   = clamp(offset - wrapper.offsetWidth * 0.8, 0, getMaxOffset());
        });
    }
    if (btnNext) {
        btnNext.addEventListener('click', () => {
            velocity = 0;
            offset   = clamp(offset + wrapper.offsetWidth * 0.8, 0, getMaxOffset());
        });
    }

    // ── Page visibility ──
    document.addEventListener('visibilitychange', () => { paused = document.hidden; });

    // ── Init ──
    buildDots();
    window.addEventListener('resize', buildDots);
    requestAnimationFrame(loop);
});
</script>