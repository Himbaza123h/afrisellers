
@php
    $ads        = $ads ?? collect();
    $uid        = 'sqad-' . ($instanceId ?? uniqid());
    $count      = $ads->count();
    $intervalMs = 4000; // ms between fades
@endphp

@if($ads->isEmpty())
    {{-- render nothing --}}
@else
<div class="w-full sticky top-4" id="{{ $uid }}-wrapper">

    {{-- Square container --}}
    <div class="relative w-full overflow-hiddenshadow-md"
         style="aspect-ratio: 1 / 1;">

        {{-- All slides stacked --}}
        @foreach($ads as $i => $ad)
            @php
                $adUrl  = $ad->cta_url && $ad->cta_url !== '#' ? $ad->cta_url : '#';
                $accent = $ad->accent ?? '#ff0808';
                $media  = $ad->media;
            @endphp

            <a href="{{ $adUrl }}"
               target="{{ $adUrl !== '#' ? '_blank' : '_self' }}"
               rel="noopener noreferrer"
               title="{{ $ad->headline }}"
               class="sqad-slide group absolute inset-0 w-full h-full block transition-opacity duration-700 ease-in-out"
               style="opacity: {{ $i === 0 ? '1' : '0' }}; z-index: {{ $i === 0 ? '2' : '1' }};">

                {{-- Background --}}
                @if($media)
                    @if($media->type === 'video')
                        <video class="absolute inset-0 w-full h-full object-cover" autoplay muted loop playsinline>
                            <source src="{{ Storage::url($media->file_path) }}">
                        </video>
                    @else
                        <img src="{{ Storage::url($media->file_path) }}"
                             alt="{{ $ad->headline }}"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    @endif
                @else
                    <div class="absolute inset-0 flex items-center justify-center"
                         style="background: {{ $accent }}20;">
                        <i class="fas fa-ad text-4xl" style="color: {{ $accent }};"></i>
                    </div>
                @endif

                {{-- Dark gradient --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

                {{-- Badge --}}
                @if($ad->badge)
                <div class="absolute top-2.5 left-2.5">
                    <span class="px-2 py-0.5 text-[9px] font-black tracking-widest uppercase rounded text-white shadow"
                          style="background: {{ $accent }};">
                        {{ $ad->badge }}
                    </span>
                </div>
                @endif

                {{-- Ad label --}}
                <div class="absolute top-2.5 right-2.5">
                    <span class="px-1.5 py-0.5 text-[8px] font-medium tracking-wide uppercase rounded bg-black/40 text-white/80 backdrop-blur-sm">
                        Ad
                    </span>
                </div>

                {{-- Text --}}
                <div class="absolute bottom-0 left-0 right-0 p-3">
                    <p class="text-white font-bold text-sm leading-tight line-clamp-2 drop-shadow">
                        {{ $ad->headline }}
                    </p>
                    @if($ad->sub_text)
                    <p class="text-white/80 text-[10px] mt-1 line-clamp-2 drop-shadow">
                        {{ $ad->sub_text }}
                    </p>
                    @endif
                    <div class="mt-2 inline-flex items-center gap-1 text-[10px] font-semibold text-white px-2.5 py-1 rounded-full"
                         style="background: {{ $accent }};">
                        Learn More
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>

            </a>
        @endforeach

        {{-- Dots (only if more than 1) --}}
        @if($count > 1)
        <div class="absolute bottom-2 right-2 z-10 flex gap-1" id="{{ $uid }}-dots">
            @foreach($ads as $i => $ad)
                <span class="sqad-dot block w-1.5 h-1.5 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-white scale-125' : 'bg-white/40' }}"></span>
            @endforeach
        </div>
        @endif

    </div>
</div>

@if($count > 1)
<script>
(function() {
    const uid     = '{{ $uid }}';
    const total   = {{ $count }};
    const delay   = {{ $intervalMs }};
    let   current = 0;

    const wrapper = document.getElementById(uid + '-wrapper');
    if (!wrapper) return;

    const slides = wrapper.querySelectorAll('.sqad-slide');
    const dots   = wrapper.querySelectorAll('.sqad-dot');

    function goTo(next) {
        // Fade out current
        slides[current].style.opacity = '0';
        slides[current].style.zIndex  = '1';
        dots[current]?.classList.remove('bg-white', 'scale-125');
        dots[current]?.classList.add('bg-white/40');

        current = next;

        // Fade in next
        slides[current].style.opacity = '1';
        slides[current].style.zIndex  = '2';
        dots[current]?.classList.add('bg-white', 'scale-125');
        dots[current]?.classList.remove('bg-white/40');
    }

    setInterval(function() {
        goTo((current + 1) % total);
    }, delay);
})();
</script>
@endif

@endif
