<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feature extends Model
{
    public const VALUE_TYPES = ['boolean', 'number', 'number_or_unlimited', 'text'];

    protected $fillable = [
        'name',
        'description',
        'slug',
        'feature_key',
        'value_type',
        'status',
        'is_supported',
    ];

    protected function casts(): array
    {
        return [
            'is_supported' => 'boolean',
        ];
    }

    public function planFeatures(): HasMany
    {
        return $this->hasMany(PlanFeature::class, 'feature_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Value semantics for plan assignments (primary: database column).
     */
    public function resolvedValueType(): string
    {
        $t = $this->value_type ?? null;
        if (is_string($t) && $t !== '' && in_array($t, self::VALUE_TYPES, true)) {
            return $t;
        }

        $def = config('membership_feature_keys.'.$this->feature_key);
        $fallback = is_array($def) ? (string) ($def['type'] ?? 'text') : 'text';

        return in_array($fallback, self::VALUE_TYPES, true) ? $fallback : 'text';
    }
}
