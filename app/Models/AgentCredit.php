<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentCredit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'agent_id',
        'total_credits',
    ];

    protected $casts = [
        'total_credits' => 'decimal:2',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
