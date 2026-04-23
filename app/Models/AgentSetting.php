<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentSetting extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'agent_settings';

    protected $fillable = [
        'user_id',
        'timezone', 'language', 'currency', 'date_format',
        'notify_email', 'notify_new_vendor', 'notify_commission',
        'notify_ticket_reply', 'notify_payout', 'notify_expiry',
        'payout_method', 'bank_name', 'bank_account_number',
        'bank_account_name', 'bank_branch',
        'mobile_money_number', 'mobile_money_provider', 'paypal_email',
        'commission_payout_threshold', 'commission_payout_frequency',
        'two_factor_enabled', 'two_factor_secret',
    ];

    protected $casts = [
        'notify_email'        => 'boolean',
        'notify_new_vendor'   => 'boolean',
        'notify_commission'   => 'boolean',
        'notify_ticket_reply' => 'boolean',
        'notify_payout'       => 'boolean',
        'notify_expiry'       => 'boolean',
        'two_factor_enabled'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
