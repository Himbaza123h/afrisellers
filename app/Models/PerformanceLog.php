<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceLog extends Model
{
    protected $fillable = [
        'product_id',
        'ip_address',
        'type',
        'tracked_date',
    ];

    protected $casts = [
        'tracked_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
