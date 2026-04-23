<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentPayout extends Model
{
use HasFactory, SoftDeletes,LogsActivity;

    protected $table = 'agent_payouts';

    protected $fillable = [
        'agent_id',
        'payout_number',
        'amount',
        'currency',
        'payment_method',
        'account_details',
        'status',
        'notes',
        'admin_notes',
        'processed_at',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'account_details'=> 'array',
        'processed_at'   => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────
    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ── Helpers ────────────────────────────────────────────────────────
    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending']);
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    // ── Static ─────────────────────────────────────────────────────────
    public static function generatePayoutNumber(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->orderByDesc('id')->first();
        $next = $last ? intval(substr($last->payout_number, -5)) + 1 : 1;
        return 'PAY-' . $year . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
