<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'value',
        'type',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];
}
