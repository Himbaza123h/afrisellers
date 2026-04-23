<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Configuration extends Model
{
use HasFactory, SoftDeletes,LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'configurations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
protected $fillable = [
    'unique_id',
    'title',
    'type',
    'files',
    'value',
    'is_active',
    'country_id',
];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'files' => 'array',

    ];

    /**
     * Get the value attribute based on type.
     *
     * @param  string  $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        return match($this->type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'array', 'json' => json_decode($value, true),
            'text', 'string' => $value,
            'file', 'string' => $value,
            default => $value,
        };
    }

    /**
     * Set the value attribute based on type.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['value'] = null;
            return;
        }

        $this->attributes['value'] = match($this->type) {
            'array', 'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };
    }

    /**
     * Scope a query to only include active configurations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive configurations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to filter by unique_id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $uniqueId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUniqueId($query, $uniqueId)
    {
        return $query->where('unique_id', $uniqueId);
    }

    /**
     * Scope a query to filter by type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get a configuration value by unique_id.
     *
     * @param  string  $uniqueId
     * @param  mixed  $default
     * @return mixed
     */
    public static function getValue($uniqueId, $default = null)
    {
        $config = self::active()->byUniqueId($uniqueId)->first();

        return $config ? $config->value : $default;
    }

    public function country()
{
    return $this->belongsTo(\App\Models\Country::class);
}

    /**
     * Set a configuration value by unique_id.
     *
     * @param  string  $uniqueId
     * @param  mixed  $value
     * @param  string  $type
     * @return self
     */
    public static function setValue($uniqueId, $value, $type = 'string')
    {
        return self::updateOrCreate(
            ['unique_id' => $uniqueId],
            [
                'value' => $value,
                'type' => $type,
                'is_active' => true,
            ]
        );
    }
}
