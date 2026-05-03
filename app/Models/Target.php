<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Target extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'target_type',
        'target_amount',
        'prize',
        'end_at',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'end_at'        => 'datetime',
    ];



    public function isExpired(): bool
    {
        return $this->end_at && $this->end_at->isPast();
    }
    

public function progressFor(float $amount): int
    {
        return (int) min(100, round(($amount / max((float) $this->target_amount, 1)) * 100));
    }

    public function rewards()
    {
        return $this->hasMany(\App\Models\AgentReward::class);
    }
}
