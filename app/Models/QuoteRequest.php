<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_profile_id',
        'product_id',
        'name',
        'email',
        'phone',
        'quantity',
        'message',
        'status',
        'ip_address',
        'user_agent',
        'contacted_at',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
    ];

    /**
     * Get the business profile that received this quote request.
     */
    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    /**
     * Get the product (if specific product was requested).
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Mark as contacted.
     */
    public function markAsContacted()
    {
        $this->update([
            'status' => 'contacted',
            'contacted_at' => now(),
        ]);
    }
}
