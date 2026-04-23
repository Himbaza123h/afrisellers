@extends('layouts.app')

@section('title', $article->title . ' - Article')

@section('content')
<div class="bg-gray-50 to-gray-100 min-h-screen py-3">
    <div class="container mx-auto px-3 max-w-6xl">

        <!-- Breadcrumb -->
        <nav class="flex mb-3 text-[10px]" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-1.5">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-[#ff0808]">Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="mx-1 w-2 h-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('business-profile.show', $businessProfile->id) }}" class="text-gray-700 hover:text-[#ff0808]">{{ $businessProfile->business_name }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="mx-1 w-2 h-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500">{{ Str::limit($article->title, 30) }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Article Header -->
        <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden mb-3">
            <div class="px-3 py-4">
                <!-- Category Badge -->
                @if($article->category)
                <div class="mb-2">
                    <span class="inline-flex items-center gap-1 bg-[#ff0808] text-white text-[10px] font-semibold px-2 py-0.5 rounded-full">
                        <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                        </svg>
                        {{ $article->category }}
                    </span>
                </div>
                @endif

                <!-- Title -->
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 leading-tight">
                    {{ $article->title }}
                </h1>

                <!-- Meta Information -->
                <div class="flex flex-wrap gap-3 items-center text-[10px] text-gray-600 mb-3">
                    <!-- Author -->
                    <div class="flex items-center gap-1.5">
                        <div class="w-7 h-7 rounded-full bg-[#ff0808] to-red-600 flex items-center justify-center text-white font-bold text-[10px]">
                            {{ $article->author_initials }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-[10px]">{{ $article->author_name ?? $article->user->name }}</p>
                            @if($article->author_title)
                            <p class="text-[10px] text-gray-500">{{ $article->author_title }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $article->formatted_published_date }}</span>
                    </div>

                    <!-- Reading Time -->
                    @if($article->reading_time_minutes)
                    <div class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $article->reading_time_minutes }} min read</span>
                    </div>
                    @endif

                    <!-- Views -->
                    <div class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>{{ number_format($article->views_count) }} views</span>
                    </div>
                </div>

                <!-- Share Buttons -->
<!-- Share + Like Buttons -->
@php
    $userHasLiked = \App\Models\ArticleLike::where('article_id', $article->id)
                        ->where('ip_address', request()->ip())
                        ->exists();
@endphp
<div class="flex gap-1.5 flex-wrap">
    <button onclick="shareArticle('facebook')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-semibold rounded transition-all">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
        Share
    </button>
    <button onclick="shareArticle('twitter')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-600 hover:bg-gray-700 text-white text-[10px] font-semibold rounded transition-all">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
        </svg>
        Tweet
    </button>

    <!-- Like Button -->
    <button id="like-btn"
        onclick="toggleLike()"
        class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-semibold rounded transition-all border
        {{ $userHasLiked ? 'bg-[#ff0808] text-white border-[#ff0808]' : 'border-gray-300 text-gray-700 hover:border-[#ff0808] hover:text-[#ff0808]' }}">
        <svg id="like-icon" class="w-3 h-3" fill="{{ $userHasLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <span id="like-count">{{ number_format($article->likes_count) }}</span>
    </button>
</div>

<script>
    const likeUrl = "{{ route('business-profile.products.like', ['businessProfileId' => $businessProfile->id, 'articleSlug' => $article->slug]) }}";
    const csrfToken = "{{ csrf_token() }}";

    function toggleLike() {
        const btn = document.getElementById('like-btn');
        const icon = document.getElementById('like-icon');
        const count = document.getElementById('like-count');

        btn.disabled = true;

        fetch(likeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        })
        .then(res => res.json())
        .then(data => {
            count.textContent = Number(data.count).toLocaleString();
            if (data.liked) {
                btn.className = 'inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-semibold rounded transition-all border bg-[#ff0808] text-white border-[#ff0808]';
                icon.setAttribute('fill', 'currentColor');
            } else {
                btn.className = 'inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-semibold rounded transition-all border border-gray-300 text-gray-700 hover:border-[#ff0808] hover:text-[#ff0808]';
                icon.setAttribute('fill', 'none');
            }
        })
        .finally(() => { btn.disabled = false; });
    }

    function shareArticle(platform) {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.title);
        const links = {
            facebook: `https://www.facebook.com/sharer/sharer.php?u=${url}`,
            twitter: `https://twitter.com/intent/tweet?url=${url}&text=${title}`,
        };
        window.open(links[platform], '_blank', 'width=600,height=400');
    }
</script>
            </div>
        </div>

        <!-- Featured Image -->
        @if($article->featured_image)
        <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden mb-3">
            <div class="relative group cursor-pointer" onclick="openImageModal('{{ $article->featured_image }}')">
                <img src="{{ $article->featured_image }}"
                     alt="{{ $article->title }}"
                     class="w-full h-52 object-cover transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                    <svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>
            @if($article->featured_image_caption)
            <div class="px-3 py-1.5 bg-gray-50 border-t border-gray-200">
                <p class="text-[10px] text-gray-600">
                    <span class="font-semibold">Photo:</span> {{ $article->featured_image_caption }}
                </p>
            </div>
            @endif
        </div>
        @endif
        @include('frontend.home.sections.article-ads')

        <!-- Tab Navigation -->
        <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden mb-3">
            <div class="border-b border-gray-200">
                <nav class="flex px-2 space-x-4" aria-label="Tabs">
                    <button class="tab-button px-1 py-2.5 text-[10px] font-medium text-[#ff0808] border-b-2 border-[#ff0808]" data-tab="article">
                        <div class="flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Article</span>
                        </div>
                    </button>
                    <button class="tab-button px-1 py-2.5 text-[10px] font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300" data-tab="comments">
                        <div class="flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            <span>Comments</span>
                            <span class="ml-1 text-[10px] bg-gray-200 text-gray-700 px-1.5 py-0.5 rounded-full">{{ $article->comments_count }}</span>
                        </div>
                    </button>
                    <button class="tab-button px-1 py-2.5 text-[10px] font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300" data-tab="related">
                        <div class="flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                            <span>Related Articles</span>
                        </div>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="relative">
            <!-- Article Tab -->
            <div id="article-tab" class="tab-content">
                <div class="bg-white rounded border border-gray-200 shadow-sm p-4">
                    <!-- Article Content -->
                    <div class="prose max-w-none">
                        {!! $article->content !!}
                    </div>

                    <!-- Author Bio -->
                    @if($article->author_bio)
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="flex gap-2.5 items-start">
                            <div class="w-12 h-12 rounded-full bg-[#ff0808] to-red-600 flex items-center justify-center text-white font-bold text-base flex-shrink-0">
                                {{ $article->author_initials }}
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 mb-1">About the Author</h3>
                                <p class="text-[10px] font-semibold text-[#ff0808] mb-1.5">{{ $article->author_name ?? $article->user->name }}@if($article->author_title) - {{ $article->author_title }}@endif</p>
                                <p class="text-gray-700 text-[10px] leading-relaxed mb-2">
                                    {{ $article->author_bio }}
                                </p>
                                @if($article->author_social_links)
                                <div class="flex gap-1.5">
                                    @foreach($article->author_social_links as $platform => $link)
                                    <a href="{{ $link }}" target="_blank" class="text-gray-600 hover:text-[#ff0808]">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                    </a>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Tags -->
                    @if($article->tags && count($article->tags) > 0)
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <h4 class="text-[10px] font-semibold text-gray-900 mb-1.5">Tags:</h4>
                        <div class="flex flex-wrap gap-1">
                            @foreach($article->tags as $tag)
                            <span class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-medium rounded-full cursor-pointer transition-colors">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Comments Tab -->
            <div id="comments-tab" class="tab-content hidden">
                <div class="bg-white rounded border border-gray-200 shadow-sm p-3">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Comments & Discussion
                        </h2>
                        <span class="text-[10px] text-gray-500">{{ $article->comments_count }} comments</span>
                    </div>

                    <!-- Add Comment Form -->
                    @if($article->allow_comments)
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="text-[10px] font-semibold text-gray-900 mb-2">Leave a Comment</h3>
                        @if(session('success'))
                        <div class="mb-2 px-2 py-1.5 bg-green-50 border border-green-200 text-green-700 text-[10px] rounded">
                            {{ session('success') }}
                        </div>
                        @endif
                        <form method="POST" action="{{ route('business-profile.products.comment.store', ['businessProfileId' => $businessProfile->id, 'articleSlug' => $article->slug]) }}">
                            @csrf
                            <div class="mb-2">
                                <label class="block text-[10px] font-medium text-gray-700 mb-1">Your Name</label>
                                <input type="text" name="commenter_name" required class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff0808] text-[10px]" placeholder="Enter your name">
                            </div>
                            <div class="mb-2">
                                <label class="block text-[10px] font-medium text-gray-700 mb-1">Your Comment</label>
                                <textarea name="content" rows="3" required class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff0808] text-[10px]" placeholder="Share your thoughts..."></textarea>
                            </div>
                            <button type="submit" class="px-2.5 py-1 bg-[#ff0808] hover:bg-[#dd0606] text-white text-[10px] font-semibold rounded transition-all">
                                Post Comment
                            </button>
                        </form>
                    </div>
                    @endif

                    <!-- Comments List -->
                    @if($article->comments->count() > 0)
                    <div class="space-y-3">
                        @foreach($article->comments()->approved()->topLevel()->latest()->get() as $comment)
                        <div class="border-b border-gray-200 pb-3">
                            <div class="flex gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0">
                                    {{ $comment->commenter_initials }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-1">
                                        <div>
                                            <p class="font-semibold text-gray-900 text-[10px]">{{ $comment->commenter_name }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $comment->formatted_date }}</p>
                                        </div>
                                    </div>
                                    <p class="text-gray-700 text-[10px] leading-relaxed mb-1.5">
                                        {{ $comment->content }}
                                    </p>
                                    {{-- <div class="flex items-center gap-2 text-[10px]">
                                        <span class="text-gray-500">{{ $comment->likes_count }} likes</span>
                                    </div> --}}

                                    <!-- Replies -->
                                    @foreach($comment->replies()->approved()->get() as $reply)
                                    <div class="mt-2 ml-5 pl-2 border-l-2 border-gray-200">
                                        <div class="flex gap-1.5">
                                            <div class="w-7 h-7 rounded-full bg-green-500 to-green-600 flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0">
                                                {{ $reply->commenter_initials }}
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-start justify-between mb-0.5">
                                                    <div>
                                                        <p class="font-semibold text-gray-900 text-[10px]">
                                                            {{ $reply->commenter_name }}
                                                            @if($reply->user_id == $article->user_id)
                                                            <span class="ml-1 px-1.5 py-0.5 bg-[#ff0808] text-white text-[10px] rounded">Author</span>
                                                            @endif
                                                        </p>
                                                        <p class="text-[10px] text-gray-500">{{ $reply->formatted_date }}</p>
                                                    </div>
                                                </div>
                                                <p class="text-gray-700 text-[10px] leading-relaxed mb-1">
                                                    {{ $reply->content }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6">
                        <p class="text-gray-500 text-xs">No comments yet. Be the first to comment!</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Related Articles Tab -->
            <div id="related-tab" class="tab-content hidden">
                <div class="bg-white rounded border border-gray-200 shadow-sm p-3">
                    <h2 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#ff0808]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        Related Articles
                    </h2>

                    @if($relatedArticles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                        @foreach($relatedArticles as $relatedArticle)
                        <article class="border border-gray-200 rounded overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-[#ff0808] group">
                            <div class="relative h-28 overflow-hidden">
                                <a href="{{ route('business-profile.products.singleArticle', ['businessProfileId' => $businessProfile->id, 'articleSlug' => $relatedArticle->slug]) }}">
                                    <img src="{{ $relatedArticle->featured_image ?? 'https://images.pexels.com/photos/1072824/pexels-photo-1072824.jpeg?auto=compress&cs=tinysrgb&w=400' }}"
                                         alt="{{ $relatedArticle->title }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </a>
                            </div>
                            <div class="p-2.5">
                                @if($relatedArticle->category)
                                <span class="inline-block px-1.5 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-semibold rounded mb-1">{{ $relatedArticle->category }}</span>
                                @endif
                                <h3 class="text-xs font-bold text-gray-900 mb-1 group-hover:text-[#ff0808] transition-colors line-clamp-2">
                                    {{ $relatedArticle->title }}
                                </h3>
                                <p class="text-[10px] text-gray-600 mb-1.5 line-clamp-2">
                                    {{ $relatedArticle->excerpt }}
                                </p>
                                <div class="flex items-center justify-between text-[10px]">
                                    <span class="text-gray-500">📅 {{ $relatedArticle->formatted_published_date }}</span>
                                    <a href="{{ route('business-profile.products.singleArticle', ['businessProfileId' => $businessProfile->id, 'articleSlug' => $relatedArticle->slug]) }}" class="text-[#ff0808] font-semibold group-hover:underline">Read More →</a>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6">
                        <p class="text-gray-500 text-xs">No related articles found.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-90">
    <button onclick="closeImageModal()" class="absolute top-3 right-3 text-white hover:text-gray-300 z-50">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <div class="flex items-center justify-center h-full p-3">
        <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-full object-contain">
    </div>
</div>

<style>
    .tab-content {
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .tab-content.hidden {
        display: none;
    }

    .prose p {
        margin-bottom: 0.75rem;
        font-size: 0.75rem;
        line-height: 1.4;
    }

    .prose h2 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-size: 1rem;
        font-weight: 700;
    }

    .prose h3 {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        font-weight: 700;
    }

    .prose ul, .prose ol {
        font-size: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .prose li {
        margin-bottom: 0.25rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        function switchTab(tabName) {
            tabButtons.forEach(button => {
                button.className = 'tab-button px-1 py-2.5 text-[10px] font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300';
            });

            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            const activeButton = Array.from(tabButtons).find(btn => btn.getAttribute('data-tab') === tabName);
            if (activeButton) {
                activeButton.className = 'tab-button px-1 py-2.5 text-[10px] font-medium text-[#ff0808] border-b-2 border-[#ff0808]';
            }

            const activeContent = document.getElementById(`${tabName}-tab`);
            if (activeContent) {
                activeContent.classList.remove('hidden');
            }

            localStorage.setItem('activeArticleTab', tabName);
        }

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabName = button.getAttribute('data-tab');
                switchTab(tabName);
            });
        });

        const savedTab = localStorage.getItem('activeArticleTab') || 'article';
        switchTab(savedTab);
    });

    function openImageModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');

        if (modal && modalImage) {
            modalImage.src = imageSrc;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });

    document.getElementById('imageModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });
</script>
@endsection
