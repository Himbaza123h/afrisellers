<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use App\Models\Article;

class ArticleAnalytics extends Model
{
    
    protected $table = 'article_analytics';

    protected $fillable = [
        'article_id',
        'views', 'unique_views', 'shares',
        'likes', 'comments_count', 'bookmarks',
        'total_read_time', 'avg_read_time', 'completion_rate',
        'recorded_date', 'period',
    ];

    protected $casts = [
        'recorded_date'   => 'date',
        'avg_read_time'   => 'decimal:2',
        'completion_rate' => 'decimal:2',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public static function alltime(int $articleId): self
    {
        return static::firstOrCreate(
            ['article_id' => $articleId, 'period' => 'alltime', 'recorded_date' => null]
        );
    }

    public static function today(int $articleId): self
    {
        return static::firstOrCreate(
            ['article_id' => $articleId, 'period' => 'daily', 'recorded_date' => now()->toDateString()]
        );
    }
}
