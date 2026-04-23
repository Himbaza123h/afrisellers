<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;

class VendorTrial extends Model
{
    protected $fillable = [
        'vendor_id',
        'user_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'is_active' => 'boolean',
    ];

    public function vendor() { return $this->belongsTo(\App\Models\Vendor\Vendor::class); }
    public function user()   { return $this->belongsTo(User::class); }
    public function plan()   { return $this->belongsTo(Plan::class); }
}
