<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'product_id',
        'image_url',
        'thumbnail_url',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

public function getImageUrlAttribute($value): string
{
    if (!$value) return '';
    if (str_starts_with($value, 'http')) return $value;
    if (str_starts_with($value, '/storage')) return $value;
    return Storage::url($value);
}

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeThumbnails($query)
    {
        return $query->whereNotNull('thumbnail_url');
    }
}
