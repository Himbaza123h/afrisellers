<?php

namespace App\Models\Vendor;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'phone',
        'country',
        'city',
        'business_registration_number',
        'business_registration_doc',
        'owner_id_document',
        'owner_full_name',
        'verification_status',
        'account_status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeActive($query)
    {
        return $query->where('account_status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    // Helper methods
    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }

    public function isActive()
    {
        return $this->account_status === 'active';
    }

    public function isPending()
    {
        return $this->verification_status === 'pending';
    }

    public function verify()
    {
        $this->update(['verification_status' => 'verified']);
    }

    public function reject()
    {
        $this->update(['verification_status' => 'rejected']);
    }

    public function suspend()
    {
        $this->update(['account_status' => 'suspended']);
    }

    public function activate()
    {
        $this->update(['account_status' => 'active']);
    }
}
