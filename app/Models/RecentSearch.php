<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecentSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_query',
        'result_count',
        'clicked_result_id',
        'user_id',
        'search_count',
    ];

    protected $casts = [
        'result_count' => 'integer',
        'search_count' => 'integer',
    ];

    /**
     * Get the user who performed the search
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the clicked search result
     */
    public function clickedResult(): BelongsTo
    {
        return $this->belongsTo(GlobalSearchIndex::class, 'clicked_result_id');
    }

    /**
     * Scope to get popular searches
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->orderByDesc('search_count')->limit($limit);
    }

    /**
     * Scope to get recent searches
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    /**
     * Scope to get searches by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Increment search count or create new record
     */
    public static function recordSearch(string $searchQuery, int $resultCount, ?int $userId = null): self
    {
        $search = self::where('search_query', $searchQuery)
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->first();

        if ($search) {
            $search->increment('search_count');
            $search->update(['result_count' => $resultCount]);
            return $search;
        }

        return self::create([
            'search_query' => $searchQuery,
            'result_count' => $resultCount,
            'user_id' => $userId,
            'search_count' => 1,
        ]);
    }
}
