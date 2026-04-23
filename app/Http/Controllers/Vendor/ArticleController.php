<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Analytics\ArticleAnalytics;



class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::where('user_id', auth()->id())->with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $articles = $query->paginate(12);

        // Stats
        $stats = [
            'total' => Article::where('user_id', auth()->id())->count(),
            'published' => Article::where('user_id', auth()->id())->where('status', 'published')->count(),
            'draft' => Article::where('user_id', auth()->id())->where('status', 'draft')->count(),
            'featured' => Article::where('user_id', auth()->id())->where('is_featured', true)->count(),
            'total_views' => Article::where('user_id', auth()->id())->sum('views_count'),
            'total_comments' => Article::where('user_id', auth()->id())->sum('comments_count'),
        ];

        $categories = Article::getAllCategories();

        return view('vendor.articles.index', compact('articles', 'stats', 'categories'));
    }

    public function print(Request $request)
    {
        $query = Article::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $articles = $query->orderBy('created_at', 'desc')->get();

        return view('vendor.articles.print', compact('articles'));
    }

public function create()
{
    if ($redirect = $this->checkArticleLimit()) {
        return $redirect;
    }
    $categories = Article::getAllCategories();
    return view('vendor.articles.create', compact('categories'));
}


    public function toggleAutoApprove(Article $article)
{
    $article->update([
        'auto_approve_comments' => !$article->auto_approve_comments,
    ]);

    return redirect()->back()->with('success', 'Auto-approve setting updated.');
}

public function store(Request $request)
{
    if ($redirect = $this->checkArticleLimit()) {
        return $redirect;
    }

    $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'author_name' => 'nullable|string|max:255',
            'author_title' => 'nullable|string|max:255',
            'author_bio' => 'nullable|string',
            'author_avatar' => 'nullable|image|max:2048',
            'author_social_links' => 'nullable|array',
            'featured_image' => 'nullable|image|max:5120',
            'featured_image_caption' => 'nullable|string|max:255',
            'gallery_images.*' => 'nullable|image|max:5120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
        ]);

        // Handle file uploads
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('articles/featured', 'public');
        }

        if ($request->hasFile('author_avatar')) {
            $validated['author_avatar'] = $request->file('author_avatar')->store('articles/authors', 'public');
        }

        if ($request->hasFile('gallery_images')) {
            $galleryImages = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryImages[] = $image->store('articles/gallery', 'public');
            }
            $validated['gallery_images'] = $galleryImages;
        }

        // Set user_id
        $validated['user_id'] = auth()->id();

        // Auto-publish if status is published and no publish date set
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $article = Article::create($validated);

        return redirect()->route('vendor.articles.show', $article)
            ->with('success', 'Article created successfully!');
    }

    public function show(Article $article)
    {
        $article->load('user', 'comments.user', 'comments.replies.user');

        // Increment views
        $article->incrementViews();

        // Related articles
        $relatedArticles = Article::related($article)->limit(3)->get();

        ArticleAnalytics::alltime($article->id)->increment('views');
ArticleAnalytics::today($article->id)->increment('views');

        // All categories and tags
        $categories = Article::getAllCategories();
        $tags = Article::getAllTags();

        return view('vendor.articles.show', compact('article', 'relatedArticles', 'categories', 'tags'));
    }

    public function edit(Article $article)
    {
        $categories = Article::getAllCategories();
        return view('vendor.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'author_name' => 'nullable|string|max:255',
            'author_title' => 'nullable|string|max:255',
            'author_bio' => 'nullable|string',
            'author_avatar' => 'nullable|image|max:2048',
            'author_social_links' => 'nullable|array',
            'featured_image' => 'nullable|image|max:5120',
            'featured_image_caption' => 'nullable|string|max:255',
            'gallery_images.*' => 'nullable|image|max:5120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'remove_featured_image' => 'boolean',
            'remove_author_avatar' => 'boolean',
        ]);

        // Handle featured image removal
        if ($request->boolean('remove_featured_image') && $article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
            $validated['featured_image'] = null;
        }

        // Handle new featured image
        if ($request->hasFile('featured_image')) {
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('articles/featured', 'public');
        }

        // Handle author avatar removal
        if ($request->boolean('remove_author_avatar') && $article->author_avatar) {
            Storage::disk('public')->delete($article->author_avatar);
            $validated['author_avatar'] = null;
        }

        // Handle new author avatar
        if ($request->hasFile('author_avatar')) {
            if ($article->author_avatar) {
                Storage::disk('public')->delete($article->author_avatar);
            }
            $validated['author_avatar'] = $request->file('author_avatar')->store('articles/authors', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            $galleryImages = $article->gallery_images ?? [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryImages[] = $image->store('articles/gallery', 'public');
            }
            $validated['gallery_images'] = $galleryImages;
        }

        // Auto-publish if status changed to published
        if ($validated['status'] === 'published' && $article->status !== 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return redirect()->route('vendor.articles.show', $article)
            ->with('success', 'Article updated successfully!');
    }

    public function destroy(Article $article)
    {
        // Delete associated files
        if ($article->featured_image) {
            Storage::disk('public')->delete($article->featured_image);
        }
        if ($article->author_avatar) {
            Storage::disk('public')->delete($article->author_avatar);
        }
        if ($article->gallery_images) {
            foreach ($article->gallery_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Article deleted successfully!');
    }

    public function toggleStatus(Article $article)
    {
        $newStatus = $article->status === 'published' ? 'draft' : 'published';

        $article->update([
            'status' => $newStatus,
            'published_at' => $newStatus === 'published' ? ($article->published_at ?? now()) : $article->published_at,
        ]);

        return back()->with('success', 'Article status updated successfully!');
    }

    public function toggleFeatured(Article $article)
    {
        $article->update([
            'is_featured' => !$article->is_featured,
        ]);

        return back()->with('success', 'Article featured status updated successfully!');
    }

private function checkArticleLimit()
{
    $user = auth()->user();

    $hasSubscription = \App\Models\Subscription::where('seller_id', $user->id)
        ->where('status', 'active')
        ->exists();

    $hasTrial = \App\Models\VendorTrial::where('user_id', $user->id)
        ->where('is_active', true)
        ->where('ends_at', '>=', now())
        ->exists();

    if ($hasSubscription || $hasTrial) {
        return null;
    }

    $articlesThisMonth = Article::where('user_id', $user->id)
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();

    if ($articlesThisMonth >= 1) {
        return redirect()->route('vendor.articles.index')
            ->with('error', 'You have reached your free article limit for this month. Please upgrade your plan to create more.');
    }

    return null;
}
}
