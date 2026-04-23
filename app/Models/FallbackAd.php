<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;

class FallbackAd extends Model
{
    use LogsActivity;
    protected $fillable = [
        'position', 'type', 'media', 'bg',
        'headline', 'sub_text', 'cta_url',
        'badge', 'overlay', 'accent',
        'pattern', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'pattern'   => 'boolean',
        'is_active' => 'boolean',
    ];

    // ── All valid positions ───────────────────────────────────────
public static function positions(): array
{
    return [
        // ── Top Hero ─────────────────────────────────────────────────
        'homepage_header'           => 'Top Hero – Left',
        'homepage_right'          => 'Top Hero – Right',

        // ── Under Hero ───────────────────────────────────────────────
        'homepage_sidebar'         => 'Under Hero – Left',
        'under-hero-right'        => 'Under Hero – Right',

        // ── Middle Section ───────────────────────────────────────────
        'middle-section-left'     => 'Middle Section – Left',
        'middle-section-right'    => 'Middle Section – Right',

        // ── Middle Square ────────────────────────────────────────────
        'middle-square-left'      => 'Middle Square – Left',
        'middle-square-right'     => 'Middle Square – Right',
    ];
}

    // ── All valid types ───────────────────────────────────────────
    public static function types(): array
    {
        return [
            'image' => 'Image (JPG/PNG/WebP)',
            'gif'   => 'GIF',
            'video' => 'Video (MP4)',
            'text'  => 'Text / Brand',
        ];
    }

    // ── Scope: active only ────────────────────────────────────────
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    // ── Get fallbacks for a position as array (drop-in for dummy) ─
    public static function forPosition(string $position): array
    {
        return static::active()
            ->where('position', $position)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn($ad) => [
                'real_id' => null,
                'type'    => $ad->type,
                'media'   => $ad->media,
                'bg'      => $ad->bg,
                'headline'=> $ad->headline,
                'sub'     => $ad->sub_text,
                'cta_url' => $ad->cta_url,
                'badge'   => $ad->badge,
                'overlay' => $ad->overlay,
                'accent'  => $ad->accent,
                'pattern' => $ad->pattern,
            ])
            ->toArray();
    }
}
