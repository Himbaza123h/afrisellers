<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Advertisement extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id', 'title', 'type', 'position',
        'media_path', 'media_url', 'destination_url',
        'headline', 'sub_text', 'badge_text',
        'bg_gradient', 'accent_color', 'overlay_color',
        'width', 'height',
        'start_date', 'end_date', 'duration_days',
        'amount_paid', 'paid_at',
        'status', 'rejection_reason', 'approved_at', 'approved_by',
        'impressions', 'clicks',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'paid_at'     => 'datetime',
        'approved_at' => 'datetime',
    ];

    // ── Positions with labels and sizes ──────────────────────────
    public static function positions(): array
    {
        return [
            'homepage_header'  => ['label' => 'Homepage Header',        'size' => '800 × 112px',  'slots' => 2],
            'homepage_right'   => ['label' => 'Homepage Right Strip',   'size' => '110 × full height', 'slots' => 8],
            'homepage_sidebar' => ['label' => 'Homepage Sidebar',       'size' => '300 × 250px',  'slots' => 9],
            'company_profile'  => ['label' => 'Company Profile Page',   'size' => '800 × 112px',  'slots' => 2],
            'product_detail'   => ['label' => 'Product Detail Page',    'size' => '800 × 112px',  'slots' => 2],
            'article_detail'   => ['label' => 'Article Detail Page',    'size' => '900 × 112px',  'slots' => 2],

        ];
    }

    public static function types(): array
    {
        return ['image' => 'Image (JPG/PNG/WebP)', 'gif' => 'GIF', 'video' => 'Video (MP4)', 'text' => 'Text/Brand'];
    }

    // ── Relationships ─────────────────────────────────────────────
    public function user()       { return $this->belongsTo(User::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }

    // ── Scopes ────────────────────────────────────────────────────
    public function scopeRunning($q)  { return $q->where('status', 'running')->where('end_date', '>=', now()); }
    public function scopePending($q)  { return $q->where('status', 'pending'); }
    public function scopeApproved($q) { return $q->where('status', 'approved'); }

    // ── Helpers ───────────────────────────────────────────────────
    public function isRunning(): bool  { return $this->status === 'running' && $this->end_date >= now(); }
    public function isExpired(): bool  { return $this->end_date && $this->end_date < now(); }
    public function isPending(): bool  { return $this->status === 'pending'; }

    public function getMediaUrlAttribute($value): ?string
    {
        if ($value) return $value;
        if ($this->media_path) return Storage::disk('public')->url($this->media_path);
        return null;
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->end_date) return 0;
        return max(0, now()->diffInDays($this->end_date, false));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'running'  => 'green',
            'approved' => 'blue',
            'pending'  => 'yellow',
            'rejected' => 'red',
            'expired'  => 'gray',
            default    => 'gray',
        };
    }
}
