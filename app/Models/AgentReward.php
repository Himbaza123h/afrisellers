<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentReward extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'agent_id',
        'target_id',
        'period_key',
        'credits_awarded',
        'status',
        'claimed_at',
    ];

    protected $casts = [
        'credits_awarded' => 'decimal:2',
        'claimed_at'      => 'datetime',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isClaimed(): bool
    {
        return $this->status === 'claimed';
    }

    // Generate the period key server-side — never trust client input
    public static function periodKeyFor(string $targetType): string
    {
        return match ($targetType) {
            'monthly' => now()->format('Y-m'),
            'weekly'  => now()->format('Y-\WW'),
            'yearly'  => now()->format('Y'),
            default   => now()->format('Y-m'),
        };
    }
}
