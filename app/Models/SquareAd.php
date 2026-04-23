<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SquareAd extends Model
{
    protected $fillable = [
        'library_id',
        'type',
        'headline',
        'sub_text',
        'cta_url',
        'badge',
        'accent',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(AdMedia::class, 'library_id');
    }
}
