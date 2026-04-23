{{-- Articles Section for Homepage --}}
<section class="py-6 md:py-8 bg-blue-50">
    <div class="container px-4 mx-auto">

        {{-- Section Header --}}
        <div class="flex items-center mb-3 md:mb-4 gap-2">
            <h2 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 whitespace-nowrap">
                {{ __('Most Articles') }}
            </h2>
            <div class="flex-1 h-px bg-gray-300 to-transparent"></div>
            <a href="#" class="flex items-center gap-0.5 md:gap-1 text-[10px] md:text-xs font-semibold text-[#ff0808] hover:text-[#dd0606] transition-colors whitespace-nowrap">
                <span>{{ __('messages.view_all') }}</span>
                <svg class="w-2.5 h-2.5 md:w-3 md:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Articles Grid --}}
        @php
            $latestArticles = \App\Models\Article::where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->with('user')
                ->orderBy('published_at', 'desc')
                ->take(5)
                ->get();
        @endphp

        @if($latestArticles->count() > 0)
        <div class="grid grid-cols-2 gap-2 md:gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
            @foreach($latestArticles as $article)
            <article class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 hover:border-[#ff0808] group flex flex-col">
                {{-- Featured Image - REDUCED HEIGHT --}}
                <div class="relative h-36 overflow-hidden flex-shrink-0">
                    <a href="{{ route('business-profile.products.singleArticle', ['businessProfileId' => $article->user->businessProfile->id, 'articleSlug' => $article->slug]) }}">
                        @php
                            // Check if image starts with http:// or https://
                            $imageUrl = (str_starts_with($article->featured_image, 'http://') || str_starts_with($article->featured_image, 'https://'))
                                ? $article->featured_image
                                : Storage::url($article->featured_image);
                        @endphp
                        <img src="{{ $imageUrl ?? 'https://images.pexels.com/photos/1072824/pexels-photo-1072824.jpeg?auto=compress&cs=tinysrgb&w=600' }}"
                             alt="{{ $article->title }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                             loading="lazy">
                    </a>

                    {{-- Category Badge --}}
                    @if($article->category)
                    <div class="absolute top-2 left-2">
                        <span class="inline-flex items-center gap-1 bg-[#ff0808] text-white text-[10px] font-semibold px-2 py-0.5 rounded-full shadow-lg">
                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                            </svg>
                            {{ $article->category }}
                        </span>
                    </div>
                    @endif

                    {{-- Featured Badge --}}
                    @if($article->is_featured)
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center gap-1 bg-yellow-500 text-white text-[10px] font-semibold px-1.5 py-0.5 rounded-full shadow-lg">
                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Content - REDUCED PADDING --}}
                <div class="p-3 flex-1 flex flex-col">
                    {{-- Title --}}
                    <h3 class="text-sm font-bold text-gray-900 mb-1.5 group-hover:text-[#ff0808] transition-colors line-clamp-2 leading-tight">
                        <a href="{{ route('business-profile.products.singleArticle', ['businessProfileId' => $article->user->businessProfile->id, 'articleSlug' => $article->slug]) }}">
                            {{ $article->title }}
                        </a>
                    </h3>

                    {{-- Excerpt - REDUCED --}}
                    <p class="text-[11px] text-gray-600 mb-2 line-clamp-2 leading-relaxed flex-1">
                        {{ $article->description ?? Str::limit(strip_tags($article->content), 80) }}
                    </p>

                    {{-- Meta Info - COMPACT --}}
                    <div class="flex items-center justify-between text-xs border-t border-gray-100 pt-2 mt-auto">
                        <div class="flex items-center gap-1.5">
                            {{-- Author Avatar --}}
                            <div class="w-5 h-5 rounded-full bg-[#ff0808] to-red-600 flex items-center justify-center text-white font-bold text-[8px]">
                                {{ $article->author_initials }}
                            </div>

                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-900 text-[9px] leading-tight">{{ $article->author_name ?? $article->user->name }}</span>
                                <span class="text-gray-500 text-[8px] leading-tight">{{ $article->formatted_published_date }}</span>
                            </div>
                        </div>

                        {{-- Views --}}
                        <div class="flex items-center gap-1 text-gray-500">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span class="text-[9px]">{{ number_format($article->views_count) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Footer Stats - COMPACT --}}
                <div class="px-3 py-1.5 bg-gray-50 border-t border-gray-100">
                    <div class="flex items-center justify-between text-[9px] text-gray-600">
                        <div class="flex items-center gap-2">
                            {{-- Reading Time --}}
                            @if($article->reading_time_minutes)
                            <div class="flex items-center gap-0.5">
                                <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $article->reading_time_minutes }} min</span>
                            </div>
                            @endif

                            {{-- Comments Count --}}
                            @if($article->allow_comments)
                            <div class="flex items-center gap-0.5">
                                <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                <span>{{ $article->comments_count }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Tags Preview --}}
                        @if($article->tags && count($article->tags) > 0)
                        <div class="flex items-center gap-0.5">
                            <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span>{{ count($article->tags) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        @else
        {{-- No Articles Found --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Articles Yet</h3>
            <p class="text-sm text-gray-600 mb-4">Check back soon for the latest articles and insights.</p>
        </div>
        @endif

    </div>
</section>

{{-- Additional Styles --}}
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Ensure consistent card heights */
    article {
        min-height: 280px;
        max-height: 320px;
    }
</style>
