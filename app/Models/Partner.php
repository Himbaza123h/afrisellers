<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Partner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'logo',
        'website_url',
        'industry',
        'partner_type',
        'description',
        'sort_order',
        'is_active',
        'partner_request_id',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getLogoUrlAttribute(): string
    {
        if (!$this->logo) return '';
        if (str_starts_with($this->logo, 'http')) return $this->logo;
        return Storage::url($this->logo);
    }

        public function partnerRequest()
        {
            return $this->belongsTo(PartnerRequest::class, 'partner_request_id');
        }   

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }
}
