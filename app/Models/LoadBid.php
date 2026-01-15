<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoadBid extends Model
{
    protected $fillable = [
        'bid_number',
        'load_id',
        'transporter_id',
        'bid_amount',
        'currency',
        'estimated_delivery_days',
        'proposal',
        'vehicle_details',
        'insurance_details',
        'status',
        'valid_until',
        'accepted_at',
        'rejected_at',
        'withdrawn_at',
        'rejection_reason',
    ];

    protected $casts = [
        'bid_amount' => 'decimal:2',
        'estimated_delivery_days' => 'integer',
        'vehicle_details' => 'array',
        'insurance_details' => 'array',
        'valid_until' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'withdrawn_at' => 'datetime',
    ];

    // Relationships
    public function Userload(): BelongsTo
    {
        return $this->belongsTo(Load::class);
    }

    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeWithdrawn($query)
    {
        return $query->where('status', 'withdrawn');
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('valid_until')
                     ->where('valid_until', '<=', now());
    }

    // Accessor & Mutator
    public function getIsValidAttribute(): bool
    {
        return is_null($this->valid_until) || $this->valid_until->isFuture();
    }

    public function getIsExpiredAttribute(): bool
    {
        return !is_null($this->valid_until) && $this->valid_until->isPast();
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsAcceptedAttribute(): bool
    {
        return $this->status === 'accepted';
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    public function getIsWithdrawnAttribute(): bool
    {
        return $this->status === 'withdrawn';
    }

    // Methods
    public function accept(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function reject(string $reason = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function withdraw(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->update([
            'status' => 'withdrawn',
            'withdrawn_at' => now(),
        ]);
    }

    // Boot method for auto-generating bid_number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bid) {
            if (empty($bid->bid_number)) {
                $bid->bid_number = 'BID-' . strtoupper(uniqid());
            }
        });
    }
}
