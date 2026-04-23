<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
use HasFactory, SoftDeletes,LogsActivity;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category',
        'subject',
        'description',
        'status',
        'requires_attention',
        'priority',
        'attachments',
        'last_replied_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'attachments'     => 'array',
        'last_replied_at' => 'datetime',
        'resolved_at'     => 'datetime',
        'closed_at'       => 'datetime',
        'requires_attention' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id')->oldest();
    }

    public function latestReply()
    {
        return $this->hasOne(SupportTicketReply::class, 'ticket_id')->latest();
    }

    // ─── Scopes ───────────────────────────────────────────────────────
    public function scopeOpen($query)       { return $query->where('status', 'open'); }
    public function scopeInProgress($query) { return $query->where('status', 'in_progress'); }
    public function scopeResolved($query)   { return $query->where('status', 'resolved'); }
    public function scopeClosed($query)     { return $query->where('status', 'closed'); }

    public function scopeRequiresAttention($query) { return $query->where('requires_attention', true); }
    // ─── Helpers ──────────────────────────────────────────────────────
    public function isOpen()     { return $this->status === 'open'; }
    public function isClosed()   { return in_array($this->status, ['closed', 'resolved']); }
    public function isResolved() { return $this->status === 'resolved'; }

    public function close()
    {
        $this->update(['status' => 'closed', 'closed_at' => now()]);
    }

    public function resolve()
    {
        $this->update(['status' => 'resolved', 'resolved_at' => now()]);
    }

    public function reopen()
    {
        $this->update(['status' => 'open', 'resolved_at' => null, 'closed_at' => null]);
    }

    // ─── Static ───────────────────────────────────────────────────────
    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $num = $last
            ? intval(substr($last->ticket_number, -4)) + 1
            : 1;

        return 'TKT-' . $year . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
