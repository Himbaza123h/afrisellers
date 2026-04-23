<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketReply extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'attachments',
        'is_staff_reply',
    ];

    protected $casts = [
        'attachments'    => 'array',
        'is_staff_reply' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
