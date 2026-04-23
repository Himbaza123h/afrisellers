@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .article-card { transition: all 0.3s ease; }
    .article-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -10px rgba(0,0,0,0.15); }

    /* Tab Animation */
    .tab-content {
        animation: slideUp 0.4s ease-out;
        transform-origin: bottom;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Articles</h1>
            <p class="mt-1 text-xs text-gray-500">Manage your blog articles and content</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('vendor.articles.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-plus"></i>
                <span>New Article</span>
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-2 fade-in">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-green-900">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-2 fade-in">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-red-900">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    @endif

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 overflow-x-auto">
        <button onclick="switchTab('all')" id="tab-all" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors whitespace-nowrap">
            All ({{ number_format($stats['total']) }})
        </button>
        <button onclick="switchTab('published')" id="tab-published" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors whitespace-nowrap">
            Published ({{ number_format($stats['published']) }})
        </button>
        <button onclick="switchTab('draft')" id="tab-draft" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors whitespace-nowrap">
            Drafts ({{ number_format($stats['draft']) }})
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Articles</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 bg-blue-50 to-blue-100 rounded-lg">
                    <i class="fas fa-newspaper text-base text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Published</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['published']) }}</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 bg-green-50 to-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-base text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Drafts</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['draft']) }}</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 bg-yellow-50 to-yellow-100 rounded-lg">
                    <i class="fas fa-edit text-base text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Featured</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['featured']) }}</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 bg-purple-50 to-purple-100 rounded-lg">
                    <i class="fas fa-star text-base text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Views</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_views']) }}</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 bg-indigo-50 to-indigo-100 rounded-lg">
                    <i class="fas fa-eye text-base text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Comments</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_comments']) }}</p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 bg-pink-50 to-pink-100 rounded-lg">
                    <i class="fas fa-comments text-base text-pink-600"></i>
                </div>
            </div>
        </div>
    </div>


    <!-- Articles List Section -->
    <div id="articles-section" class="tab-content">
        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3 mb-4">
            <form method="GET" action="{{ route('vendor.articles.index') }}" class="space-y-2" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search articles..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <div>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="status" id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Status</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            <i class="fas fa-filter text-xs"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'category', 'status']))
                            <a href="{{ route('vendor.articles.index') }}" class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm">
                                <i class="fas fa-undo text-xs"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($articles as $article)
                <div class="article-card bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden" data-status="{{ $article->status }}">
                    <!-- Featured Image -->
                    <div class="relative h-40 bg-gray-100">
                        @if($article->featured_image)
                            @php
                                // Check if image starts with http:// or https://
                                $imageUrl = (str_starts_with($article->featured_image, 'http://') || str_starts_with($article->featured_image, 'https://'))
                                    ? $article->featured_image
                                    : Storage::url($article->featured_image);
                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-image text-4xl text-gray-300"></i>
                            </div>
                        @endif

                        <!-- Badges -->
                        <div class="absolute top-2 left-2 flex gap-2">
                            @if($article->is_featured)
                                <span class="px-2 py-0.5 bg-yellow-500 text-white text-xs font-bold rounded-full flex items-center gap-1">
                                    <i class="fas fa-star text-xs"></i> Featured
                                </span>
                            @endif
                            <span class="px-2 py-0.5 {{ $article->status === 'published' ? 'bg-green-500' : 'bg-gray-500' }} text-white text-xs font-bold rounded-full">
                                {{ ucfirst($article->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <div class="flex items-center gap-2 mb-2">
                            @if($article->category)
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded">
                                    {{ $article->category }}
                                </span>
                            @endif
                            <span class="text-xs text-gray-500">{{ $article->reading_time_minutes }} min read</span>
                        </div>

                        <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">
                            {{ $article->title }}
                        </h3>

                        <p class="text-xs text-gray-600 mb-3 line-clamp-2">
                            {!! $article->excerpt !!}
                        </p>

                        <!-- Meta Info -->
                        <div class="flex items-center gap-3 mb-3 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-eye"></i> {{ number_format($article->views_count) }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-comments"></i> {{ number_format($article->comments_count) }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-heart"></i> {{ number_format($article->likes_count) }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 pt-3 border-t border-gray-100">
                            <a href="{{ route('vendor.articles.show', $article) }}"
                                class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-600 rounded text-xs font-medium hover:bg-blue-100 transition-colors">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('vendor.articles.edit', $article) }}"
                                class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-gray-50 text-gray-600 rounded text-xs font-medium hover:bg-gray-100 transition-colors">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('vendor.articles.destroy', $article) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this article?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-red-50 text-red-600 rounded text-xs font-medium hover:bg-red-100 transition-colors">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                        <i class="fas fa-newspaper text-5xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 font-medium">No articles found</p>
                        <p class="text-xs text-gray-400 mt-1">Create your first article to get started</p>
                        <a href="{{ route('vendor.articles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] transition-all font-medium shadow-sm text-sm mt-4">
                            <i class="fas fa-plus"></i> New Article
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
            <div class="mt-4">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        btn.classList.add('text-gray-600');
    });

    // Add active state to selected tab
    const activeTab = document.getElementById(`tab-${tab}`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

    // Update the status filter dropdown
    const statusFilter = document.getElementById('statusFilter');

    if (tab === 'all') {
        statusFilter.value = '';
    } else if (tab === 'published') {
        statusFilter.value = 'published';
    } else if (tab === 'draft') {
        statusFilter.value = 'draft';
    }

    // Filter articles visually without reloading
    filterArticlesVisually(tab);
}

function filterArticlesVisually(status) {
    const articles = document.querySelectorAll('.article-card');
    const articlesSection = document.getElementById('articles-section');

    // Add animation class
    articlesSection.classList.remove('tab-content');
    void articlesSection.offsetWidth; // Force reflow
    articlesSection.classList.add('tab-content');

    articles.forEach(article => {
        const articleStatus = article.getAttribute('data-status');

        if (status === 'all') {
            article.style.display = '';
        } else if (status === articleStatus) {
            article.style.display = '';
        } else {
            article.style.display = 'none';
        }
    });

    // Check if any articles are visible
    const visibleArticles = Array.from(articles).filter(article => article.style.display !== 'none');
    const emptyState = document.querySelector('.col-span-full');

    if (visibleArticles.length === 0 && emptyState) {
        emptyState.style.display = '';
    } else if (emptyState) {
        emptyState.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    if (status === 'published') {
        switchTab('published');
    } else if (status === 'draft') {
        switchTab('draft');
    } else {
        switchTab('all');
    }
});
</script>
@endpush
