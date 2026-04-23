<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Ad extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'title',
        'slug',
        'description',
        'media_type',
        'media_path',
        'media_original_name',
        'media_size',
        'thumbnail_path',
        'target_url',
        'placement',
        'status',
        'starts_at',
        'ends_at',
        'impressions',
        'clicks',
        'is_admin_approved',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'starts_at'        => 'datetime',
        'ends_at'          => 'datetime',
        'approved_at'      => 'datetime',
        'is_admin_approved' => 'boolean',
    ];

    // ─── Relationships ─────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    public function getMediaUrlAttribute(): string
    {
        return asset('public/storage/' . $this->media_path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail_path) {
            return asset('public/storage/' . $this->thumbnail_path);
        }
        return null;
    }

    public function getMediaSizeFormattedAttribute(): string
    {
        $bytes = $this->media_size ?? 0;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->is_admin_approved
            && ($this->starts_at === null || $this->starts_at->lte(now()))
            && ($this->ends_at === null   || $this->ends_at->gte(now()));
    }

    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->lt(now());
    }

    public function getCtrAttribute(): float
    {
        if ($this->impressions === 0) return 0;
        return round(($this->clicks / $this->impressions) * 100, 2);
    }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('is_admin_approved', true)
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
