<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Article extends Model
{
use HasFactory, SoftDeletes,LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'content',
        'category',
        'tags',
        'author_name',
        'author_title',
        'author_bio',
        'author_avatar',
        'author_social_links',
        'views_count',
        'comments_count',
        'likes_count',
        'shares_count',
        'reading_time_minutes',
        'featured_image',
        'featured_image_caption',
        'gallery_images',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'published_at',
        'is_featured',
        'allow_comments',
        'auto_approve_comments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tags' => 'array',
        'author_social_links' => 'array',
        'gallery_images' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'auto_approve_comments' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug if not provided
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }

            // Auto-calculate reading time if not provided
            if (empty($article->reading_time_minutes) && !empty($article->content)) {
                $article->reading_time_minutes = $article->calculateReadingTime();
            }
        });

        static::updating(function ($article) {
            // Update slug if title changed
            if ($article->isDirty('title') && empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }

            // Recalculate reading time if content changed
            if ($article->isDirty('content')) {
                $article->reading_time_minutes = $article->calculateReadingTime();
            }
        });
    }

    /**
     * Get the user that owns the article.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the article.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class)->whereNull('parent_id');
    }

    /**
     * Get all comments including replies.
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function likes(): HasMany
{
    return $this->hasMany(ArticleLike::class);
}

    /**
     * Scope a query to only include published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include draft articles.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include featured articles.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to filter by tag.
     */
    public function scopeWithTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Scope a query to order by most viewed.
     */
    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    /**
     * Scope a query to order by most recent.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    /**
     * Scope a query to get related articles.
     */
    public function scopeRelated($query, $article)
    {
        return $query->published()
                     ->where('id', '!=', $article->id)
                     ->where(function ($q) use ($article) {
                         $q->where('category', $article->category);

                         // Match by tags if available
                         if (!empty($article->tags)) {
                             foreach ($article->tags as $tag) {
                                 $q->orWhereJsonContains('tags', $tag);
                             }
                         }
                     })
                     ->orderBy('published_at', 'desc');
    }

    /**
     * Increment the views count.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Increment the comments count.
     */
    public function incrementComments()
    {
        $this->increment('comments_count');
    }

    /**
     * Decrement the comments count.
     */
    public function decrementComments()
    {
        $this->decrement('comments_count');
    }

    /**
     * Increment the likes count.
     */
public function incrementLikes()
{
    $this->update(['likes_count' => $this->likes()->count()]);
}

    /**
     * Increment the shares count.
     */
    public function incrementShares()
    {
        $this->increment('shares_count');
    }

    /**
     * Calculate reading time based on content.
     */
    public function calculateReadingTime(): int
    {
        $wordsPerMinute = 200; // Average reading speed
        $wordCount = str_word_count(strip_tags($this->content));

        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Get the author's initials for avatar.
     */
    public function getAuthorInitialsAttribute(): string
    {
        $name = $this->author_name ?? $this->user->name ?? 'Unknown';
        $words = explode(' ', $name);

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($name, 0, 2));
    }

    /**
     * Get the formatted published date.
     */
    public function getFormattedPublishedDateAttribute(): string
    {
        if (!$this->published_at) {
            return 'Not published';
        }

        return $this->published_at->format('F j, Y');
    }

    /**
     * Get the excerpt from description or content.
     */
    public function getExcerptAttribute(): string
    {
        if ($this->description) {
            return $this->description;
        }

        $text = strip_tags($this->content);
        return Str::limit($text, 200);
    }

    /**
     * Check if article is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published'
               && $this->published_at !== null
               && $this->published_at->lte(now());
    }

    /**
     * Check if article is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Publish the article.
     */
    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => $this->published_at ?? now(),
        ]);
    }

    /**
     * Unpublish the article.
     */
    public function unpublish()
    {
        $this->update([
            'status' => 'draft',
        ]);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get all unique categories.
     */
    public static function getAllCategories(): array
    {
        return static::whereNotNull('category')
                     ->distinct()
                     ->pluck('category')
                     ->toArray();
    }

    /**
     * Get all unique tags.
     */
    public static function getAllTags(): array
    {
        $tags = static::whereNotNull('tags')
                      ->pluck('tags')
                      ->flatten()
                      ->unique()
                      ->values()
                      ->toArray();

        return $tags;
    }
}
