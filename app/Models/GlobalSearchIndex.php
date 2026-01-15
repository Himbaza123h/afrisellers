<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlobalSearchIndex extends Model
{
    use HasFactory;

    protected $table = 'global_search_index';

    protected $fillable = [
        'searchable_type',
        'searchable_id',
        'title',
        'description',
        'search_content',
        'url',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the owning searchable model (User, Post, Product, etc.)
     */
    public function searchable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get recent searches that clicked this result
     */
    public function recentSearches(): HasMany
    {
        return $this->hasMany(RecentSearch::class, 'clicked_result_id');
    }

    /**
     * Scope to search by query
     */
    public function scopeSearch($query, string $searchQuery)
    {
        return $query->whereFullText(['title', 'search_content'], $searchQuery)
            ->orWhere('title', 'like', "%{$searchQuery}%")
            ->orWhere('search_content', 'like', "%{$searchQuery}%");
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('searchable_type', $type);
    }
}
