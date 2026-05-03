<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class PartnerRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Original fields
        'user_id',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'website_url',
        'industry',
        'country',
        'established',
        'about_us',
        'services',
        'partner_type',
        'message',
        'logo',
        'intro',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
        'name',
        'password',
        'presence_countries',
        'vendor_user_id',
        'partner_user_id',
        'agent_user_id',
        'registered_by_agent_id',

        // ── New fields from migration ──────────────────────────
        // Basic company info
        'trading_name',
        'registration_number',
        'physical_address',

        // Branding & content
        'cover_image',
        'short_description',
        'full_description',
        'promo_video_url',

        // Contact person
        'contact_position',
        'whatsapp',

        // Social media
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'linkedin_url',
        'youtube_url',
        'tiktok_url',

        // Business type
        'business_type',

        // Operations
        'branches_count',
        'target_market',
        'countries_of_operation',
    ];

    protected $casts = [
        'reviewed_at'           => 'datetime',
        'services'              => 'array',
        'countries_of_operation' => 'array',   // ← was missing; caused save failure
    ];

    // ── Accessors ──────────────────────────────────────────────────

    public function getLogoUrlAttribute(): string
    {
        if (!$this->logo) return '';
        if (str_starts_with($this->logo, 'http')) return $this->logo;
        return Storage::url($this->logo);
    }

    public function getIntroUrlAttribute(): string
    {
        if (!$this->intro) return '';
        if (str_starts_with($this->intro, 'http')) return $this->intro;
        return Storage::url($this->intro);
    }

    public function getServicesStringAttribute(): string
    {
        if (is_array($this->services)) {
            return implode(', ', $this->services);
        }
        return (string) ($this->services ?? '');
    }

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopePending($query)  { return $query->where('status', 'pending'); }
    public function scopeApproved($query) { return $query->where('status', 'approved'); }
    public function scopeRejected($query) { return $query->where('status', 'rejected'); }

    // ── Status helpers ─────────────────────────────────────────────

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
}
