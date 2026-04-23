<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MessageGroup extends Model
{
use HasFactory, SoftDeletes,LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'type',
        'created_by',
        'avatar',
        'is_active',
        'is_locked',
        'invite_code',
        'invite_code_expires_at'
    ];



    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'invite_code_expires_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'message_group_members', 'group_id', 'user_id')
            ->withPivot(['role', 'is_muted', 'last_read_at'])
            ->withTimestamps();
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'message_group_members', 'group_id', 'user_id')
            ->wherePivot('role', 'admin')
            ->withPivot(['role', 'is_muted', 'last_read_at'])
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'group_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'group_id')->latest();
    }

    public function unreadCount($userId)
    {
        $member = $this->members()->where('user_id', $userId)->first();
        if (!$member) return 0;

        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('created_at', '>', $member->pivot->last_read_at ?? now()->subYears(10))
            ->count();
    }

    public function isAdmin($userId)
    {
        return $this->members()
            ->where('user_id', $userId)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    public function isMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    public function canSendMessage($userId)
    {
        if (!$this->isMember($userId)) {
            return false;
        }

        if ($this->is_locked && !$this->isAdmin($userId)) {
            return false;
        }

        return true;
    }

    public function generateInviteCode($expiresInDays = 7)
    {
        $this->invite_code = strtoupper(Str::random(8));
        $this->invite_code_expires_at = now()->addDays($expiresInDays);
        $this->save();

        return $this->invite_code;
    }

    public function isInviteCodeValid()
    {
        if (!$this->invite_code) {
            return false;
        }

        if ($this->invite_code_expires_at && $this->invite_code_expires_at->isPast()) {
            return false;
        }

        return true;
    }
}
