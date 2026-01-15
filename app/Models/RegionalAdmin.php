<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegionalAdmin extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'region_id',
        'user_id',
        'status',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeForRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    public function getRegionNameAttribute()
    {
        return $this->region->name ?? 'Unknown';
    }

    public function getAdminNameAttribute()
    {
        return $this->user->name ?? 'Unknown';
    }

    // Methods
    public function activate()
    {
        $this->update(['status' => 'active']);
    }

    public function deactivate()
    {
        $this->update(['status' => 'inactive']);
    }

    public function suspend()
    {
        $this->update(['status' => 'suspended']);
    }

    // Static methods
    public static function assignAdmin($regionId, $userId)
    {
        return static::create([
            'region_id' => $regionId,
            'user_id' => $userId,
            'status' => 'active',
            'assigned_at' => now(),
        ]);
    }

    public static function removeAdmin($regionId, $userId)
    {
        return static::where('region_id', $regionId)
            ->where('user_id', $userId)
            ->delete();
    }

    public static function getAdminForRegion($regionId)
    {
        return static::where('region_id', $regionId)
            ->where('status', 'active')
            ->with('user')
            ->first();
    }

    public static function getRegionsForAdmin($userId)
    {
        return static::where('user_id', $userId)
            ->where('status', 'active')
            ->with('region')
            ->get();
    }
}
