<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'user_number',
        'quantity',
        'price',
        'currency',
        'selected_variations'
    ];

    protected $casts = [
        'selected_variations' => 'array',
        'price' => 'decimal:2'
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
