<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdPlacement extends Model
{
    protected $fillable = [
        'ad_media_id', 'position', 'cta_url',
        'headline', 'sub_text',
        'is_active', 'sort_order',
        'starts_at', 'ends_at', 'created_by',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────
    public function media()
    {
        return $this->belongsTo(AdMedia::class, 'ad_media_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ────────────────────────────────────────────────────
    public function scopeActive($q)
    {
        return $q->where('is_active', true)
                 ->where(function ($q) {
                     $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                 })
                 ->where(function ($q) {
                     $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                 });
    }

    public function scopeForPosition($q, string $position)
    {
        return $q->where('position', $position);
    }

    // ── Helpers ───────────────────────────────────────────────────
    public function getIsLiveAttribute(): bool
    {
        if (! $this->is_active) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->ends_at   && $this->ends_at->isPast())    return false;
        return true;
    }

    public static function positions(): array
    {
        return FallbackAd::positions();   // single source of truth
    }

    public static function forPosition(string $position): self|null
    {
        return static::with('media')
            ->active()
            ->forPosition($position)
            ->orderBy('sort_order')
            ->first();
    }
}
