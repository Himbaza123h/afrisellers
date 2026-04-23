<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class AgentDocument extends Model
{
use HasFactory, SoftDeletes,LogsActivity;

    protected $table = 'agent_documents';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_name',
        'file_path',
        'requires_attention',
        'file_type',
        'file_size',
        'category',
        'tags',
        'is_shared',
        'expires_at',
    ];

    protected $casts = [
        'tags'       => 'array',
        'is_shared'  => 'boolean',
        'expires_at' => 'datetime',
        'requires_attention' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    // ─── Scopes ───────────────────────────────────────────────────────
    public function scopeForAgent($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeShared($query)
    {
        return $query->where('is_shared', true);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                     ->where('expires_at', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expires_at')
                     ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expires_at
            && !$this->isExpired()
            && $this->expires_at->lte(now()->addDays($days));
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return number_format($bytes / 1048576, 2)    . ' MB';
        if ($bytes >= 1024)       return number_format($bytes / 1024, 2)       . ' KB';
        return $bytes . ' B';
    }

    public function getIconAttribute(): string
    {
        return match(true) {
            str_contains($this->file_type, 'pdf')                         => 'fa-file-pdf text-red-500',
            str_contains($this->file_type, 'word') ||
            str_contains($this->file_type, 'document')                    => 'fa-file-word text-blue-600',
            str_contains($this->file_type, 'sheet') ||
            str_contains($this->file_type, 'excel') ||
            str_contains($this->file_type, 'csv')                         => 'fa-file-excel text-green-600',
            str_contains($this->file_type, 'image')                       => 'fa-file-image text-purple-500',
            str_contains($this->file_type, 'zip') ||
            str_contains($this->file_type, 'rar') ||
            str_contains($this->file_type, 'tar')                         => 'fa-file-archive text-amber-500',
            str_contains($this->file_type, 'text')                        => 'fa-file-alt text-gray-500',
            default                                                        => 'fa-file text-gray-400',
        };
    }

    public function url(): string
    {
        return Storage::url($this->file_path);
    }

    public function delete(): bool|null
    {
        // Delete the physical file when the model is deleted
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }
        return parent::delete();
    }
}
