<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceDelivery extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'plan_id',
        'subscription_id',
        'feature_key',
        'service_name',
        'status',
        'notes',
        'delivered_by',
        'delivered_at',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function deliveredBy()
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    // ── Helpers ────────────────────────────────────────────────
    // These are the feature keys that require manual delivery
    public static function manualFeatureKeys(): array
    {
        return [
            'has_advanced_seo'           => 'Advanced SEO Setup',
            'has_basic_seo'              => 'Basic SEO Setup',
            'has_international_seo'      => 'International SEO',
            'has_product_seo_optimization'=> 'Product SEO Optimization',
            'has_brand_profile_setup'    => 'Brand Profile Setup',
            'has_brand_strategy'         => 'Brand Strategy',
            'has_brand_positioning'      => 'Brand Positioning',
            'has_brand_storytelling'     => 'Brand Storytelling',
            'has_brand_domination'       => 'Brand Domination',
            'has_social_media_setup'     => 'Social Media Setup',
            'has_google_business_profile'=> 'Google Business Profile',
            'has_whatsapp_business_setup'=> 'WhatsApp Business Setup',
            'has_pr_services'            => 'PR Services',
            'has_press_releases'         => 'Press Releases',
            'has_media_coverage'         => 'Media Coverage',
            'has_reputation_management'  => 'Reputation Management',
            'has_local_branding'         => 'Local Branding',
            'has_basic_website'          => 'Basic Website',
            'has_multi_page_website'     => 'Multi-Page Website',
            'has_landing_page'           => 'Landing Page',
            'has_company_page'           => 'Company Page',
            'has_basic_graphics_design'  => 'Basic Graphics Design',
            'has_documentaries'          => 'Documentaries',
            'has_video_content'          => 'Video Content',
            'has_custom_onboarding_tools'=> 'Custom Onboarding Tools',
            'has_custom_integrations'    => 'Custom Integrations',
            'has_custom_software_integrations' => 'Custom Software Integrations',
        ];
    }
}
