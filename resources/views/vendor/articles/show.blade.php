@extends('layouts.home')

@push('styles')
<style>
    .slide-up {
        animation: slideUp 0.6s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }

    @keyframes slideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: slideUp 0.4s ease-out;
    }

    .tab-button {
        position: relative;
        transition: all 0.3s ease;
        padding: 14px 28px;
        border-bottom: 3px solid transparent;
        font-size: 14px;
        font-weight: 500;
        color: #6b7280;
    }

    .tab-button:hover {
        background: #f9fafb;
        color: #374151;
    }

    .tab-button.active {
        color: #ff0808;
        font-weight: 600;
        border-bottom-color: #ff0808;
        background: #fff5f5;
    }

    .article-content {
        line-height: 1.8;
    }

    .article-content p {
        margin-bottom: 1em;
    }

    .article-content h1, .article-content h2, .article-content h3,
    .article-content h4, .article-content h5, .article-content h6 {
        font-weight: bold;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
    }

    .article-content h1 { font-size: 2em; }
    .article-content h2 { font-size: 1.5em; }
    .article-content h3 { font-size: 1.25em; }

    .article-content ul, .article-content ol {
        margin-left: 1.5em;
        margin-bottom: 1em;
    }

    .article-content ul { list-style-type: disc; }
    .article-content ol { list-style-type: decimal; }

    .article-content blockquote {
        border-left: 4px solid #e5e7eb;
        padding-left: 1em;
        margin: 1em 0;
        color: #6b7280;
        font-style: italic;
    }

    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1.5em 0;
    }

    .article-content a {
        color: #3b82f6;
        text-decoration: underline;
    }

    .article-content code {
        background: #f3f4f6;
        padding: 0.2em 0.4em;
        border-radius: 0.25rem;
        font-size: 0.875em;
        font-family: monospace;
    }

    .article-content pre {
        background: #1f2937;
        color: #f9fafb;
        padding: 1em;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 1em 0;
    }

    .article-content pre code {
        background: transparent;
        color: inherit;
        padding: 0;
    }

    .stat-card {
        opacity: 0;
        transform: translateY(20px);
        animation: slideUp 0.5s ease-out forwards;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.15s; }
    .stat-card:nth-child(3) { animation-delay: 0.2s; }
    .stat-card:nth-child(4) { animation-delay: 0.25s; }
</style>
@endpush

@section('page-content')
<div class="space-y-5">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between slide-up">
        <div class="flex items-center gap-3">
            <a href="{{ route('vendor.articles.index') }}"
               class="p-2.5 text-gray-600 rounded-lg hover:bg-gray-100 transition-all hover:scale-105">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Article Details</h1>
                <p class="mt-1 text-sm text-gray-500">View and manage article</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('vendor.articles.edit', $article) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-semibold shadow-md text-sm hover:shadow-lg">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>
            <form action="{{ route('vendor.articles.toggle-status', $article) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 {{ $article->status === 'published' ? 'bg-gray-600' : 'bg-green-600' }} text-white rounded-lg hover:opacity-90 transition-all font-semibold shadow-md text-sm hover:shadow-lg">
                    <i class="fas fa-{{ $article->status === 'published' ? 'eye-slash' : 'check' }}"></i>
                    <span>{{ $article->status === 'published' ? 'Unpublish' : 'Publish' }}</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-xl border border-green-200 flex items-start gap-3 slide-up shadow-sm">
            <i class="fas fa-check-circle text-green-600 mt-0.5 text-lg"></i>
            <div class="flex-1">
                <p class="text-sm font-semibold text-green-900">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()"
                    class="text-green-600 hover:text-green-800 transition-colors p-1">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden slide-up" style="animation-delay: 0.2s;">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 bg-gray-50">
            <div class="flex overflow-x-auto">
                <button type="button" class="tab-button active" onclick="switchTab(0)">
                    <i class="fas fa-info-circle mr-2"></i>
                    Overview
                </button>
                <button type="button" class="tab-button" onclick="switchTab(1)">
                    <i class="fas fa-align-left mr-2"></i>
                    Content
                </button>
                <button type="button" class="tab-button" onclick="switchTab(2)">
                    <i class="fas fa-chart-line mr-2"></i>
                    Analytics
                </button>
                <button type="button" class="tab-button" onclick="switchTab(3)">
                    <i class="fas fa-search mr-2"></i>
                    SEO
                </button>
                <button type="button" class="tab-button" onclick="switchTab(4)">
                    <i class="fas fa-comments mr-2"></i>
                    Comments
                    <span class="ml-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold bg-red-100 text-red-700 rounded-full">{{ $article->allComments()->count() }}</span>
                </button>
            </div>
        </div>

        <!-- Tab Contents -->
        <div class="p-6">
            <!-- Tab 0: Overview -->
            <div class="tab-content active" id="tab-0">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content - Left Column (2/3) -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Featured Image -->
                        @if($article->featured_image)
                            @php
                                $imageUrl = (str_starts_with($article->featured_image, 'http://') || str_starts_with($article->featured_image, 'https://'))
                                    ? $article->featured_image
                                    : Storage::url($article->featured_image);
                            @endphp
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                                <img src="{{ $imageUrl }}" alt="{{ $article->title }}" class="w-full h-80 object-cover">
                                @if($article->featured_image_caption)
                                    <div class="p-4 bg-gray-50 border-t border-gray-200">
                                        <p class="text-sm text-gray-600 italic flex items-start gap-2">
                                            <i class="fas fa-quote-left text-gray-400 mt-1"></i>
                                            <span>{{ $article->featured_image_caption }}</span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Article Info -->
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                            <div class="mb-5">
                                <h2 class="text-3xl font-bold text-gray-900 mb-3">{{ $article->title }}</h2>
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($article->category)
                                        <span class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                            <i class="fas fa-folder mr-1"></i>
                                            {{ $article->category }}
                                        </span>
                                    @endif
                                    <span class="px-3 py-1.5 {{ $article->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} text-xs font-semibold rounded-full">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        {{ ucfirst($article->status) }}
                                    </span>
                                    @if($article->is_featured)
                                        <span class="px-3 py-1.5 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full flex items-center gap-1">
                                            <i class="fas fa-star"></i> Featured
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($article->description)
                                <div class="mb-5 p-4 bg-gray-50 rounded-lg border-l-4 border-blue-500">
                                    <p class="text-sm text-gray-700 leading-relaxed">{!! $article->description !!}</p>
                                </div>
                            @endif

                            <!-- Tags -->
                            @if($article->tags)
                                <div class="mb-5">
                                    <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <i class="fas fa-tags text-pink-500"></i>
                                        Tags
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($article->tags as $tag)
                                            <span class="px-3 py-1.5 bg-gradient-to-r from-gray-100 to-gray-50 text-gray-700 text-xs font-medium rounded-md border border-gray-200">
                                                #{{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Meta Info Grid -->
                            <div class="grid grid-cols-2 gap-4 pt-5 border-t border-gray-200">
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <p class="text-xs text-blue-600 font-semibold mb-1 flex items-center gap-1">
                                        <i class="fas fa-calendar"></i> Published
                                    </p>
                                    <p class="text-sm font-bold text-gray-900">
                                        {{ $article->published_at ? $article->published_at->format('M d, Y') : 'Not published' }}
                                    </p>
                                </div>
                                <div class="p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs text-green-600 font-semibold mb-1 flex items-center gap-1">
                                        <i class="fas fa-clock"></i> Reading Time
                                    </p>
                                    <p class="text-sm font-bold text-gray-900">{{ $article->reading_time_minutes }} min</p>
                                </div>
                                <div class="p-3 bg-purple-50 rounded-lg">
                                    <p class="text-xs text-purple-600 font-semibold mb-1 flex items-center gap-1">
                                        <i class="fas fa-user"></i> Author
                                    </p>
                                    <p class="text-sm font-bold text-gray-900">{{ $article->author_name ?? $article->user->name }}</p>
                                </div>
                                <div class="p-3 bg-yellow-50 rounded-lg">
                                    <p class="text-xs text-yellow-600 font-semibold mb-1 flex items-center gap-1">
                                        <i class="fas fa-comments"></i> Comments
                                    </p>
                                    <p class="text-sm font-bold text-gray-900">{{ $article->allow_comments ? 'Enabled' : 'Disabled' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Author Info -->
                        @if($article->author_name || $article->author_bio)
                            <div class="bg-purple-50 to-pink-50 rounded-xl border border-purple-200 shadow-sm p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-user-circle text-purple-600"></i>
                                    Author Information
                                </h3>
                                <div class="flex gap-4">
                                    @if($article->author_avatar)
                                        <img src="{{ Storage::url($article->author_avatar) }}" alt="{{ $article->author_name }}"
                                             class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-md">
                                    @else
                                        <div class="w-20 h-20 rounded-full bg-purple-400 to-pink-400 flex items-center justify-center text-white font-bold text-2xl shadow-md border-4 border-white">
                                            {{ $article->author_initials }}
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <p class="text-lg font-bold text-gray-900">{{ $article->author_name ?? $article->user->name }}</p>
                                        @if($article->author_title)
                                            <p class="text-sm text-purple-600 font-medium mb-2">{{ $article->author_title }}</p>
                                        @endif
                                        @if($article->author_bio)
                                            <p class="text-sm text-gray-700 leading-relaxed">{!! $article->author_bio !!}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar - Right Column (1/3) -->
                    <div class="space-y-6">
                        <!-- Quick Stats -->
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-chart-pie text-blue-600"></i>
                                Quick Stats
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <span class="text-sm text-gray-700 font-medium flex items-center gap-2">
                                        <i class="fas fa-eye text-blue-600"></i> Views
                                    </span>
                                    <span class="text-lg font-bold text-blue-600">{{ number_format($article->views_count) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <span class="text-sm text-gray-700 font-medium flex items-center gap-2">
                                        <i class="fas fa-comments text-green-600"></i> Comments
                                    </span>
                                    <span class="text-lg font-bold text-green-600">{{ number_format($article->comments_count) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <span class="text-sm text-gray-700 font-medium flex items-center gap-2">
                                        <i class="fas fa-heart text-red-600"></i> Likes
                                    </span>
                                    <span class="text-lg font-bold text-red-600">{{ number_format($article->likes_count) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <span class="text-sm text-gray-700 font-medium flex items-center gap-2">
                                        <i class="fas fa-share text-purple-600"></i> Shares
                                    </span>
                                    <span class="text-lg font-bold text-purple-600">{{ number_format($article->shares_count) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Related Articles -->
                        @if($relatedArticles->isNotEmpty())
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                                <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-newspaper text-orange-600"></i>
                                    Related Articles
                                </h3>
                                <div class="space-y-4">
                                    @foreach($relatedArticles as $related)
                                        @php
                                            $imageUrl = (str_starts_with($related->featured_image, 'http://') || str_starts_with($related->featured_image, 'https://'))
                                                ? $related->featured_image
                                                : Storage::url($related->featured_image);
                                        @endphp
                                        <a href="{{ route('vendor.articles.show', $related) }}"
                                           class="block group hover:bg-gray-50 p-2 rounded-lg transition-all">
                                            <div class="flex gap-3">
                                                @if($related->featured_image)
                                                    <img src="{{ $imageUrl }}" alt="{{ $related->title }}"
                                                         class="w-20 h-20 object-cover rounded-lg border border-gray-200 group-hover:border-blue-300 transition-all">
                                                @else
                                                    <div class="w-20 h-20 bg-gray-100 to-gray-200 rounded-lg flex items-center justify-center border border-gray-200">
                                                        <i class="fas fa-image text-2xl text-gray-400"></i>
                                                    </div>
                                                @endif
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 line-clamp-2 mb-1">
                                                        {{ $related->title }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 flex items-center gap-1">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $related->reading_time_minutes }} min read
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tab 1: Content -->
            <div class="tab-content" id="tab-1">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2 pb-4 border-b">
                        <i class="fas fa-file-alt text-blue-600"></i>
                        Article Content
                    </h2>
                    <div class="article-content prose prose-lg max-w-none">
                        {!! $article->content !!}
                    </div>
                </div>
            </div>

            <!-- Tab 2: Analytics -->
            <div class="tab-content" id="tab-2">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="stat-card bg-blue-50 to-blue-100 rounded-xl border border-blue-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-14 h-14 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg">
                                <i class="fas fa-eye text-2xl text-white"></i>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-blue-700 mb-1">Total Views</p>
                        <p class="text-3xl font-bold text-blue-900">{{ number_format($article->views_count) }}</p>
                    </div>

                    <div class="stat-card bg-green-50 to-green-100 rounded-xl border border-green-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-14 h-14 bg-green-600 rounded-lg flex items-center justify-center shadow-lg">
                                <i class="fas fa-comments text-2xl text-white"></i>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-green-700 mb-1">Comments</p>
                        <p class="text-3xl font-bold text-green-900">{{ number_format($article->comments_count) }}</p>
                    </div>

                    <div class="stat-card bg-red-50 to-red-100 rounded-xl border border-red-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-14 h-14 bg-red-600 rounded-lg flex items-center justify-center shadow-lg">
                                <i class="fas fa-heart text-2xl text-white"></i>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-red-700 mb-1">Likes</p>
                        <p class="text-3xl font-bold text-red-900">{{ number_format($article->likes_count) }}</p>
                    </div>

                    <div class="stat-card bg-purple-50 to-purple-100 rounded-xl border border-purple-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-14 h-14 bg-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                                <i class="fas fa-share text-2xl text-white"></i>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-purple-700 mb-1">Shares</p>
                        <p class="text-3xl font-bold text-purple-900">{{ number_format($article->shares_count) }}</p>
                    </div>
                </div>
            </div>

            <!-- Tab 3: SEO -->
            <div class="tab-content" id="tab-3">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2 pb-4 border-b">
                        <i class="fas fa-search text-green-600"></i>
                        SEO Information
                    </h2>

                    <div class="space-y-6">
                        <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                            <p class="text-sm font-semibold text-blue-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-heading"></i>
                                Meta Title
                            </p>
                            <p class="text-base text-gray-900">{{ $article->meta_title ?: $article->title }}</p>
                        </div>

                        <div class="p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500">
                            <p class="text-sm font-semibold text-purple-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-align-left"></i>
                                Meta Description
                            </p>
                            <p class="text-base text-gray-900">{{ $article->meta_description ?: $article->description ?: 'No meta description set' }}</p>
                        </div>

                        <div class="p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                            <p class="text-sm font-semibold text-yellow-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-key"></i>
                                Meta Keywords
                            </p>
                            <p class="text-base text-gray-900">{{ $article->meta_keywords ?: 'No keywords set' }}</p>
                        </div>

                        <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                            <p class="text-sm font-semibold text-green-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-link"></i>
                                URL Slug
                            </p>
                            <p class="text-base text-gray-900 font-mono bg-white px-4 py-2 rounded border border-gray-200">
                                {{ $article->slug }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tab 4: Comments -->
            <div class="tab-content" id="tab-4">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-comments text-green-600"></i>
                            Comments
                        </h2>
                        <!-- Auto Approve Toggle -->
                        <form action="{{ route('vendor.articles.toggle-auto-approve', $article) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-sm
                                {{ $article->auto_approve_comments ? 'bg-green-100 text-green-700 border border-green-300 hover:bg-green-200' : 'bg-gray-100 text-gray-600 border border-gray-300 hover:bg-gray-200' }}">
                                <i class="fas fa-{{ $article->auto_approve_comments ? 'toggle-on' : 'toggle-off' }} text-lg"></i>
                                Auto-Approve: {{ $article->auto_approve_comments ? 'ON' : 'OFF' }}
                            </button>
                        </form>
                    </div>

                    @php
                        $allComments = $article->allComments()->with('replies')->whereNull('parent_id')->latest()->get();
                    @endphp

                    @if($allComments->isEmpty())
                        <div class="text-center py-12">
                            <i class="fas fa-comment-slash text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-sm">No comments yet.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($allComments as $comment)
                            <div class="border border-gray-200 rounded-xl p-4 {{ $comment->status === 'pending' ? 'bg-yellow-50 border-yellow-200' : ($comment->status === 'rejected' ? 'bg-red-50 border-red-200 opacity-60' : 'bg-white') }}">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                        {{ $comment->commenter_initials }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between flex-wrap gap-2 mb-1">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-bold text-gray-900">{{ $comment->commenter_name }}</p>
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                                    {{ $comment->status === 'approved' ? 'bg-green-100 text-green-700' : ($comment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                    {{ ucfirst($comment->status) }}
                                                </span>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $comment->formatted_date }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700 mb-3">{{ $comment->content }}</p>
                                        <div class="flex items-center gap-2">
                                            @if($comment->status !== 'approved')
                                            <form action="{{ route('articles.comments.approve', $comment) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-all">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            @endif
                                            @if($comment->status !== 'rejected')
                                            <form action="{{ route('articles.comments.reject', $comment) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-all">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                            @endif
                                            <span class="text-xs text-gray-500 flex items-center gap-1 ml-2">
                                                <i class="fas fa-heart text-red-400"></i> {{ $comment->likes_count }}
                                            </span>
                                        </div>

                                        <!-- Replies -->
                                        @if($comment->replies->count() > 0)
                                        <div class="mt-3 space-y-2 pl-4 border-l-2 border-gray-200">
                                            @foreach($comment->replies as $reply)
                                            <div class="flex items-start gap-2">
                                                <div class="w-7 h-7 rounded-full bg-purple-400 to-purple-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                                    {{ $reply->commenter_initials }}
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-0.5">
                                                        <p class="text-xs font-bold text-gray-900">{{ $reply->commenter_name }}</p>
                                                        <span class="px-1.5 py-0.5 text-xs font-semibold rounded-full
                                                            {{ $reply->status === 'approved' ? 'bg-green-100 text-green-700' : ($reply->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                            {{ ucfirst($reply->status) }}
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-gray-700">{{ $reply->content }}</p>
                                                    <div class="flex gap-2 mt-1">
                                                        @if($reply->status !== 'approved')
                                                        <form action="{{ route('articles.comments.approve', $reply) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-xs text-green-600 hover:underline font-semibold">Approve</button>
                                                        </form>
                                                        @endif
                                                        @if($reply->status !== 'rejected')
                                                        <form action="{{ route('articles.comments.reject', $reply) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-xs text-red-600 hover:underline font-semibold">Reject</button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
// Tab Switching
function switchTab(index) {
    // Remove active class from all tabs and contents
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

    // Add active class to selected tab and content
    document.querySelectorAll('.tab-button')[index].classList.add('active');
    document.getElementById('tab-' + index).classList.add('active');
}
</script>
@endpush
