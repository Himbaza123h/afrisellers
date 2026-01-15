<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


// app/Models/Load.php
class Load extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'load_number',
        'user_id',
        'origin_address',
        'origin_city',
        'origin_state',
        'origin_country_id',
        'origin_latitude',
        'origin_longitude',
        'destination_address',
        'destination_city',
        'destination_state',
        'destination_country_id',
        'destination_latitude',
        'destination_longitude',
        'cargo_type',
        'cargo_description',
        'weight',
        'weight_unit',
        'volume',
        'volume_unit',
        'quantity',
        'packaging_type',
        'special_requirements',
        'pickup_date',
        'pickup_time_start',
        'pickup_time_end',
        'delivery_date',
        'delivery_time_start',
        'delivery_time_end',
        'budget',
        'currency',
        'pricing_type',
        'status',
        'assigned_transporter_id',
        'winning_bid_id',
        'assigned_at',
        'picked_up_at',
        'delivered_at',
        'cancelled_at',
        'documents',
        'tracking_number',
        'notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'origin_latitude' => 'decimal:8',
        'origin_longitude' => 'decimal:8',
        'destination_latitude' => 'decimal:8',
        'destination_longitude' => 'decimal:8',
        'weight' => 'decimal:2',
        'volume' => 'decimal:2',
        'quantity' => 'integer',
        'special_requirements' => 'array',
        'pickup_date' => 'datetime',
        'pickup_time_start' => 'datetime',
        'pickup_time_end' => 'datetime',
        'delivery_date' => 'datetime',
        'delivery_time_start' => 'datetime',
        'delivery_time_end' => 'datetime',
        'budget' => 'decimal:2',
        'assigned_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'documents' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function originCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'origin_country_id');
    }

    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    public function assignedTransporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class, 'assigned_transporter_id');
    }

    public function winningBid(): BelongsTo
    {
        return $this->belongsTo(LoadBid::class, 'winning_bid_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(LoadBid::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewable_id')
            ->where('reviewable_type', self::class);
    }

public function assignToTransporter($transporterId, $bidId)
{
    $this->update([
        'status' => 'assigned',
        'assigned_transporter_id' => $transporterId,
        'winning_bid_id' => $bidId,
        'assigned_at' => now(),
    ]);

    // Update the winning bid
    $winningBid = LoadBid::find($bidId);
    if ($winningBid) {
        $winningBid->accept();
    }

    // Reject other bids
    $this->bids()
        ->where('id', '!=', $bidId)
        ->where('status', 'pending')  // Only reject pending bids
        ->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
}

    public function markAsPickedUp()
    {
        $this->update([
            'status' => 'in_transit',
            'picked_up_at' => now(),
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', ['posted', 'bidding']);
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }
}
