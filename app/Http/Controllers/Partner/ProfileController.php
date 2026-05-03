<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    private const SECTIONS = [
        [
            'label'  => 'Company Info',
            'route'  => 'partner.company.show',
            'edit'   => 'partner.company.edit',
            'icon'   => 'fas fa-building',
            'color'  => 'text-blue-600',
            'bg'     => 'bg-blue-50',
            'fields' => ['company_name','trading_name','registration_number','established','country','physical_address','website_url','partner_type'],
        ],
        [
            'label'  => 'Branding',
            'route'  => 'partner.branding.show',
            'edit'   => 'partner.branding.edit',
            'icon'   => 'fas fa-paint-brush',
            'color'  => 'text-purple-600',
            'bg'     => 'bg-purple-50',
            'fields' => ['logo','cover_image','short_description','full_description'],
        ],
        [
            'label'  => 'Contact Details',
            'route'  => 'partner.contact.show',
            'edit'   => 'partner.contact.edit',
            'icon'   => 'fas fa-address-card',
            'color'  => 'text-green-600',
            'bg'     => 'bg-green-50',
            'fields' => ['contact_name','contact_position','email','phone','whatsapp'],
        ],
        [
            'label'  => 'Social Media',
            'route'  => 'partner.social.show',
            'edit'   => 'partner.social.edit',
            'icon'   => 'fas fa-share-alt',
            'color'  => 'text-pink-600',
            'bg'     => 'bg-pink-50',
            'fields' => ['facebook_url','instagram_url','twitter_url','linkedin_url','youtube_url','tiktok_url'],
        ],
        [
            'label'  => 'Business Type',
            'route'  => 'partner.business.show',
            'edit'   => 'partner.business.edit',
            'icon'   => 'fas fa-briefcase',
            'color'  => 'text-amber-600',
            'bg'     => 'bg-amber-50',
            'fields' => ['industry','business_type','services'],
        ],
        [
            'label'  => 'Operations',
            'route'  => 'partner.operations.show',
            'edit'   => 'partner.operations.edit',
            'icon'   => 'fas fa-globe-africa',
            'color'  => 'text-teal-600',
            'bg'     => 'bg-teal-50',
            'fields' => ['presence_countries','branches_count','target_market','countries_of_operation'],
        ],
    ];

    public function show()
    {
        $partner = auth()->user()->partnerRequest;
        $user    = auth()->user();

        $totalFields  = 0;
        $filledFields = 0;
        $sections     = [];

        foreach (self::SECTIONS as $section) {
            $sectionTotal  = count($section['fields']);
            $sectionFilled = 0;
            foreach ($section['fields'] as $field) {
                if (!empty($partner?->{$field})) $sectionFilled++;
            }
            $section['total']    = $sectionTotal;
            $section['filled']   = $sectionFilled;
            $section['missing']  = $sectionTotal - $sectionFilled;
            $section['percent']  = $sectionTotal > 0 ? round(($sectionFilled / $sectionTotal) * 100) : 0;
            $section['complete'] = $sectionFilled === $sectionTotal;
            $totalFields  += $sectionTotal;
            $filledFields += $sectionFilled;
            $sections[] = $section;
        }

        $overallPercent  = $totalFields  > 0 ? round(($filledFields / $totalFields) * 100) : 0;
        $completeSections = collect($sections)->where('complete', true)->count();

        $stats = [
            'sections'  => count($sections),
            'complete'  => $completeSections,
            'incomplete'=> count($sections) - $completeSections,
        ];

        return view('partner.profile.show', compact(
            'partner', 'user', 'sections', 'overallPercent', 'totalFields', 'filledFields', 'stats'
        ));
    }

    public function edit()
    {
        $partner = auth()->user()->partnerRequest;
        $user    = auth()->user();
        return view('partner.profile.edit', compact('partner', 'user'));
    }
}
