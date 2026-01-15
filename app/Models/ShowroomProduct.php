<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShowroomProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'showroom_product';

    protected $fillable = [
        'showroom_id',
        'product_id',
        'user_id',
        'added_at',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    // Relationships
    public function showroom()
    {
        return $this->belongsTo(Showroom::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeInShowroom($query, $showroomId)
    {
        return $query->where('showroom_id', $showroomId);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($showroomProduct) {
            if (empty($showroomProduct->added_at)) {
                $showroomProduct->added_at = now();
            }
        });
    }
}
