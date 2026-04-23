<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UISection extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'ui_sections';

    protected $fillable = [
        'name',
        'section_key',
        'is_active',
        'is_slide',
        'is_fade',
        'is_flip',
        'number_items',
        'allow_manual',
        'manual_items',
        'sort_order',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'is_slide'     => 'boolean',
        'is_fade'      => 'boolean',
        'is_flip'      => 'boolean',
        'allow_manual' => 'boolean',
        'manual_items' => 'array',
    ];

    // ── Predefined section definitions ────────────────────────
    const SECTIONS = [
        'hero_section' => [
            'name'          => 'Hero Section',
            'allow_manual'  => false,
        ],
        'browse_by_regions' => [
            'name'          => 'Browse by Regions',
            'allow_manual'  => false,
        ],
        'weekly_special_offers' => [
            'name'          => 'Weekly Special Offers',
            'allow_manual'  => true,
        ],
        'hot_deals' => [
            'name'          => 'Hot Deals',
            'allow_manual'  => true,
        ],
        'most_popular_suppliers' => [
            'name'          => 'Most Popular Suppliers',
            'allow_manual'  => true,
        ],
        'trending_products' => [
            'name'          => 'Trending Products',
            'allow_manual'  => true,
        ],
    ];

    // ── Validation: only one animation can be true ─────────────
    public function setAnimationMode(string $mode): void
    {
        $this->is_slide = $mode === 'slide';
        $this->is_fade  = $mode === 'fade';
        $this->is_flip  = $mode === 'flip';
    }

    public function getAnimationMode(): string
    {
        if ($this->is_slide) return 'slide';
        if ($this->is_fade)  return 'fade';
        if ($this->is_flip)  return 'flip';
        return 'none';
    }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
