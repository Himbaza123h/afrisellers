<?php

namespace App\Models;

use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'country_id',
        'company_name',
        'phone',
        'phone_code',
        'city',
        'commission_rate',
        'commission_earned',
        'total_sales',
        'account_status',
        'email_verified',
        'email_verified_at',
        'email_verification_token',
    ];

    protected $casts = [
        'email_verified'    => 'boolean',
        'email_verified_at' => 'datetime',
        'commission_rate'   => 'decimal:2',
        'commission_earned' => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function businessProfile()
    {
        return $this->hasOne(BusinessProfile::class, 'user_id', 'user_id');
    }

    /** All vendors whose agent_id = this agent's user_id */
    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'agent_id', 'user_id');
    }

    /** All business profiles linked through those vendors */
    public function businessProfiles()
    {
        return $this->hasManyThrough(
            BusinessProfile::class,
            Vendor::class,
            'agent_id',          // FK on vendors → users.id (agent's user_id)
            'id',                // FK on business_profiles
            'user_id',           // local key on agents
            'business_profile_id'
        );
    }

    // ── Status helpers ───────────────────────────────────────────────

    public function activate()
    {
        $this->update(['account_status' => 'active']);
    }

    public function suspend()
    {
        $this->update(['account_status' => 'suspended']);
    }

    public function verifyEmail()
    {
        $this->update([
            'email_verified'    => true,
            'email_verified_at' => now(),
        ]);
    }

    public function isActive()   { return $this->account_status === 'active'; }
    public function isPending()  { return $this->account_status === 'pending'; }
    public function isSuspended(){ return $this->account_status === 'suspended'; }
}
