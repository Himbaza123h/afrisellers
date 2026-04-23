<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AdMedia extends Model
{
    protected $table = 'ad_media';

    protected $fillable = [
        'name', 'original_name', 'file_path', 'disk',
        'mime_type', 'type', 'file_size', 'thumbnail_path', 'uploaded_by',
    ];

    // ── Relationships ─────────────────────────────────────────────
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function placements()
    {
        return $this->hasMany(AdPlacement::class);
    }

    // ── Accessors ─────────────────────────────────────────────────
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->file_path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail_path) {
            return Storage::disk($this->disk)->url($this->thumbnail_path);
        }
        return null;
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024)       return $bytes . ' B';
        if ($bytes < 1_048_576)  return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1_048_576, 1) . ' MB';
    }

    public function getIsImageAttribute(): bool
    {
        return in_array($this->type, ['image', 'gif']);
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->type === 'video';
    }

    // ── Static helpers ────────────────────────────────────────────
    public static function typeFromMime(string $mime): string
    {
        return match (true) {
            $mime === 'image/gif'              => 'gif',
            str_starts_with($mime, 'image/')   => 'image',
            str_starts_with($mime, 'video/')   => 'video',
            default                            => 'document',
        };
    }

    public static function allowedMimes(): array
    {
        return [
            'image/jpeg', 'image/png', 'image/webp', 'image/gif',
            'video/mp4', 'video/webm',
            'application/pdf',
        ];
    }

    public static function maxUploadMb(): int
    {
        return 50;
    }
}
