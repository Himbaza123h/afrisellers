<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
use HasFactory, SoftDeletes,LogsActivity;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'status',
        'subject',
        'content',
        'recipient_count',
        'open_count',
        'click_count',
        'scheduled_at',
        'sent_at',
    ];

    protected $casts = [
        'scheduled_at'    => 'datetime',
        'sent_at'         => 'datetime',
        'recipient_count' => 'integer',
        'open_count'      => 'integer',
        'click_count'     => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('sent_at', now()->year)
                     ->whereMonth('sent_at', now()->month);
    }

    // ── Helpers ───────────────────────────────────────────────
    public function getOpenRateAttribute(): float
    {
        if ($this->recipient_count === 0) return 0;
        return round($this->open_count / $this->recipient_count * 100, 2);
    }

    public function getClickRateAttribute(): float
    {
        if ($this->recipient_count === 0) return 0;
        return round($this->click_count / $this->recipient_count * 100, 2);
    }

    public function isSent(): bool
    {
        return !is_null($this->sent_at);
    }
}
