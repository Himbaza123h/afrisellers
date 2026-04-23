<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeature extends Model
{
    protected $with = ['feature'];

    /**
     * So JSON / API output (e.g. vendor subscription modals) includes the key from the catalog.
     */
    protected $appends = [
        'feature_key',
    ];

    protected $fillable = [
        'plan_id',
        'feature_id',
        'feature_value',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id');
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    public function getFeatureKeyAttribute(): ?string
    {
        return $this->feature?->feature_key;
    }
}
