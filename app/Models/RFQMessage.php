<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFQMessage extends Model
{
    use HasFactory;

    protected $table = 'rfq_messages';

    protected $fillable = [
        'rfq_id',
        'user_id',
        'message',
        'sender_type',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function rfq()
    {
        return $this->belongsTo(RFQs::class, 'rfq_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helper methods
    public function isFromVendor()
    {
        return $this->sender_type === 'vendor';
    }

    public function isFromAdmin()
    {
        return $this->sender_type === 'admin';
    }

    public function isFromBuyer()
    {
        return $this->sender_type === 'buyer';
    }
}
