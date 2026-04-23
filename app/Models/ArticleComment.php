<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ArticleComment extends Model
{
use HasFactory, SoftDeletes,LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_id',
        'user_id',
        'parent_id',
        'commenter_name',
        'commenter_email',
        'content',
        'likes_count',
        'status',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Increment article comment count when creating
        static::created(function ($comment) {
            $comment->article->incrementComments();
        });

        // Decrement article comment count when deleting
        static::deleted(function ($comment) {
            $comment->article->decrementComments();
        });
    }

    /**
     * Get the article that owns the comment.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the user that owns the comment (if authenticated).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment (for replies).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ArticleComment::class, 'parent_id');
    }

    /**
     * Get the replies for this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ArticleComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Scope a query to only include approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending comments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include top-level comments (not replies).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Increment the likes count.
     */
    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    /**
     * Check if this is a reply.
     */
    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    /**
     * Approve the comment.
     */
    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    /**
     * Reject the comment.
     */
    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Mark as spam.
     */
    public function markAsSpam()
    {
        $this->update(['status' => 'spam']);
    }

    /**
     * Get the commenter's initials for avatar.
     */
    public function getCommenterInitialsAttribute(): string
    {
        $name = $this->commenter_name ?? 'Guest';
        $words = explode(' ', $name);

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($name, 0, 2));
    }

    /**
     * Get the formatted created date.
     */
    public function getFormattedDateAttribute(): string
    {
        $now = now();
        $diff = $this->created_at->diffInSeconds($now);

        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' ' . Str::plural('minute', $minutes) . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' ' . Str::plural('hour', $hours) . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' ' . Str::plural('day', $days) . ' ago';
        } else {
            return $this->created_at->format('M j, Y');
        }
    }
}
