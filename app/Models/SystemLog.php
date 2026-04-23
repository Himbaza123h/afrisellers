<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SystemLog extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'system_logs';

    // No soft deletes — logs are permanent
    public $timestamps = true;
    const UPDATED_AT = null; // logs only have created_at

    protected $fillable = [
        'user_id',
        'country_id',
        'action',
        'module',
        'entity_type',
        'entity_id',
        'description',
        'metadata',
        'level',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata'   => 'array',
        'created_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────
    public function scopeLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeCritical($query)
    {
        return $query->where('level', 'critical');
    }

    public function scopeErrors($query)
    {
        return $query->whereIn('level', ['error', 'critical']);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    // ─── Static Logging Helpers ───────────────────────────────────────
    public static function log(
        string $level,
        string $description,
        array  $options = []
    ): self {
        return static::create([
            'user_id'     => $options['user_id']     ?? Auth::id(),
            'country_id'  => $options['country_id']  ?? Auth::user()?->country_id,
            'action'      => $options['action']      ?? 'event',
            'module'      => $options['module']      ?? null,
            'entity_type' => $options['entity_type'] ?? null,
            'entity_id'   => $options['entity_id']   ?? null,
            'description' => $description,
            'metadata'    => $options['metadata']    ?? null,
            'level'       => $level,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ]);
    }

    public static function info(string $description, array $options = []): self
    {
        return static::log('info', $description, $options);
    }

    public static function warning(string $description, array $options = []): self
    {
        return static::log('warning', $description, $options);
    }

    public static function error(string $description, array $options = []): self
    {
        return static::log('error', $description, $options);
    }

    public static function critical(string $description, array $options = []): self
    {
        return static::log('critical', $description, $options);
    }

    // ─── Accessors ────────────────────────────────────────────────────
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getColorAttribute(): string
    {
        return match($this->level) {
            'critical' => 'red',
            'error'    => 'orange',
            'warning'  => 'amber',
            default    => 'blue',
        };
    }

    public function getIconAttribute(): string
    {
        return match($this->level) {
            'critical' => 'fa-times-circle',
            'error'    => 'fa-exclamation-circle',
            'warning'  => 'fa-exclamation-triangle',
            default    => 'fa-info-circle',
        };
    }

    public function getLevelBadgeAttribute(): string
    {
        return match($this->level) {
            'critical' => 'bg-red-100 text-red-700',
            'error'    => 'bg-orange-100 text-orange-700',
            'warning'  => 'bg-amber-100 text-amber-700',
            default    => 'bg-blue-100 text-blue-700',
        };
    }
}
