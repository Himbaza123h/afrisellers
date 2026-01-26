<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Referral extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agent_id',
        'user_id',
        'referral_code',
        'name',
        'email',
        'phone',
        'status',
        'registered_at',
        'total_purchases',
        'notes',
    ];

    protected $casts = [
        'registered_at' => 'date',
        'total_purchases' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Boot method to generate referral code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($referral) {
            if (empty($referral->referral_code)) {
                $referral->referral_code = static::generateUniqueReferralCode();
            }
        });
    }

    // Generate unique referral code
    public static function generateUniqueReferralCode()
    {
        do {
            $code = 'REF-' . strtoupper(Str::random(8));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    // Relationships
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    // Helper Methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function activate()
    {
        $this->update([
            'status' => 'active',
            'registered_at' => $this->registered_at ?? now(),
        ]);
    }

    public function deactivate()
    {
        $this->update(['status' => 'inactive']);
    }

    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    public function getTotalCommissionsAttribute()
    {
        return $this->commissions()->sum('commission_amount');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'pending' => 'yellow',
            'inactive' => 'gray',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function getStatusBadgeAttribute()
    {
        $colors = [
            'active' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'inactive' => 'bg-gray-100 text-gray-800',
            'rejected' => 'bg-red-100 text-red-800',
        ];

        return $colors[$this->status] ?? $colors['inactive'];
    }
}
