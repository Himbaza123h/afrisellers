<?php
// app/Models/Message.php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
use HasFactory, SoftDeletes,LogsActivity;

    protected $fillable = [
        'group_id',
        'sender_id',
        'receiver_id',
        'message',
        'type',
        'attachments',
        'reply_to',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function group()
    {
        return $this->belongsTo(MessageGroup::class, 'group_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to');
    }

    public function reads()
    {
        return $this->hasMany(MessageRead::class);
    }

    public function markAsRead($userId)
    {
        MessageRead::firstOrCreate([
            'message_id' => $this->id,
            'user_id' => $userId,
        ], [
            'read_at' => now(),
        ]);

        if ($this->receiver_id == $userId) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }
}
