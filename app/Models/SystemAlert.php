<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemAlert extends Model
{
use HasFactory, SoftDeletes,LogsActivity;

    protected $fillable = [
        'title',
        'message',
        'details',
        'type',
        'severity',
        'status',
        'country_id',
        'created_by',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the country that owns the alert.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the user who created the alert.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who resolved the alert.
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope for active alerts
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for resolved alerts
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope for dismissed alerts
     */
    public function scopeDismissed($query)
    {
        return $query->where('status', 'dismissed');
    }

    /**
     * Scope for critical alerts
     */
    public function scopeCritical($query)
    {
        return $query->where('type', 'critical');
    }

    /**
     * Scope for alerts by country
     */
    public function scopeForCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope for global alerts
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('country_id');
    }

    /**
     * Mark alert as resolved
     */
    public function markAsResolved($userId = null)
    {
        return $this->update([
            'status' => 'resolved',
            'type' => 'resolved',
            'resolved_by' => $userId,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Dismiss alert
     */
    public function dismiss()
    {
        return $this->update(['status' => 'dismissed']);
    }

    /**
     * Check if alert is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if alert is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Get time ago string
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get icon based on type
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'critical' => 'fa-times-circle',
            'high' => 'fa-exclamation-triangle',
            'warning' => 'fa-exclamation',
            'resolved' => 'fa-check-circle',
            default => 'fa-info-circle',
        };
    }

    /**
     * Get color based on type
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'critical' => 'red',
            'high' => 'orange',
            'warning' => 'yellow',
            'resolved' => 'green',
            default => 'blue',
        };
    }
}
