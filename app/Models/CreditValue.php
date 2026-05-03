<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditValue extends Model
{
    use SoftDeletes;

    protected $fillable = ['value'];

    protected $casts = [
        'value' => 'decimal:2',
    ];
}
