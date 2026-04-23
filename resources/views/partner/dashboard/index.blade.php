@extends('layouts.home')

@section('page-content')

@php
    $partner = auth()->user()->partnerRequest;

    $sections = [
        ['label' => 'Company Info',    'route' => 'partner.company.show',    'fields' => ['company_name','trading_name','registration_number','established','country','physical_address','website_url']],
        ['label' => 'Branding',        'route' => 'partner.branding.show',   'fields' => ['logo','cover_image','short_description','full_description']],
        ['label' => 'Contact Details', 'route' => 'partner.contact.show',    'fields' => ['contact_name','contact_position','email','phone']],
        ['label' => 'Social Media',    'route' => 'partner.social.show',     'fields' => ['facebook_url','instagram_url','twitter_url','linkedin_url']],
        ['label' => 'Business Type',   'route' => 'partner.business.show',   'fields' => ['industry','business_type','services']],
        ['label' => 'Operations',      'route' => 'partner.operations.show', 'fields' => ['presence_countries','branches_count','target_market','countries_of_operation']],
    ];

    $totalFields = 0;
    $filledFields = 0;
    foreach ($sections as &$section) {
        $sectionTotal  = count($section['fields']);
        $sectionFilled = 0;
        foreach ($section['fields'] as $field) {
            $val = $partner?->{$field};
            if (!empty($val)) $sectionFilled++;
        }
        $section['total']    = $sectionTotal;
        $section['filled']   = $sectionFilled;
        $section['percent']  = $sectionTotal > 0 ? round(($sectionFilled / $sectionTotal) * 100) : 0;
        $section['complete'] = $sectionFilled === $sectionTotal;
        $totalFields  += $sectionTotal;
        $filledFields += $sectionFilled;
    }
    unset($section);

    $overallPercent = $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;
@endphp

{{-- Welcome Header --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <p class="text-xs text-gray-500 mb-1">Welcome back,</p>
            <h1 class="text-xl font-bold text-gray-900 uppercase">{{ auth()->user()->name }}</h1>
            @if($partner?->company_name)
                <p class="text-sm text-gray-500 mt-0.5">{{ $partner->company_name }} &mdash; {{ $partner->partner_type ?? 'Partner' }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm w-fit">
            @if($partner && $overallPercent === 100)
                <span class="flex items-center gap-1.5 text-green-600 font-bold text-xs">
                    <span class="w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse"></span>
                    Profile Complete
                </span>
            @else
                <span class="flex items-center gap-1.5 text-amber-600 font-bold text-xs">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                    Profile Incomplete
                </span>
            @endif
        </div>
    </div>
</div>

{{-- Overall Completion Banner --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        {{-- Logo --}}
        <div class="flex-shrink-0">
            @if($partner?->logo)
                <img src="{{ Storage::url($partner->logo) }}" alt="Logo"
                     class="w-16 h-16 rounded-lg object-cover border border-gray-200">
            @else
                <div class="w-16 h-16 rounded-lg bg-red-50 flex items-center justify-center">
                    <i class="fas fa-handshake text-[#ff0808] text-2xl"></i>
                </div>
            @endif
        </div>

        {{-- Progress --}}
        <div class="flex-1">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-bold text-gray-900">Profile Completion</p>
                <span class="text-sm font-black {{ $overallPercent === 100 ? 'text-green-600' : 'text-[#ff0808]' }}">
                    {{ $overallPercent }}%
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                <div class="h-2 rounded-full transition-all duration-500 {{ $overallPercent === 100 ? 'bg-green-500' : 'bg-[#ff0808]' }}"
                     style="width: {{ $overallPercent }}%"></div>
            </div>
            <p class="text-xs text-gray-500">
                {{ $filledFields }} of {{ $totalFields }} fields completed.
                @if($overallPercent < 100)
                    Complete your profile to be listed as an official Afrisellers partner.
                @else
                    Your profile is fully complete!
                @endif
            </p>
        </div>

        @if($overallPercent < 100)
            <a href="{{ route('partner.company.edit') }}"
               class="flex-shrink-0 px-4 py-2 bg-[#ff0808] text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-all">
                Complete Profile
            </a>
        @endif
    </div>
</div>

{{-- Quick Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-globe-africa text-teal-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Countries</p>
                <p class="text-xl font-black text-gray-900">{{ $partner?->presence_countries ?? '—' }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-code-branch text-blue-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Branches</p>
                <p class="text-xl font-black text-gray-900">{{ $partner?->branches_count ?? '—' }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-alt text-amber-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Since</p>
                <p class="text-xl font-black text-gray-900">{{ $partner?->established ?? '—' }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-indigo-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Target</p>
                <p class="text-sm font-black text-gray-900">{{ $partner?->target_market ?? '—' }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Profile Sections Grid --}}
<div class="mb-6">
    <h2 class="text-sm font-bold text-gray-900 mb-4">Profile Sections</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($sections as $section)
        <a href="{{ route($section['route']) }}"
           class="bg-white rounded-xl border {{ $section['complete'] ? 'border-green-200' : 'border-gray-200' }} shadow-sm p-5 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-bold text-gray-900">{{ $section['label'] }}</p>
                @if($section['complete'])
                    <span class="w-6 h-6 bg-green-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-check text-green-600 text-xs"></i>
                    </span>
                @else
                    <span class="w-6 h-6 bg-amber-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-pencil-alt text-amber-600 text-xs"></i>
                    </span>
                @endif
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-2">
                <div class="h-1.5 rounded-full {{ $section['complete'] ? 'bg-green-500' : 'bg-[#ff0808]' }}"
                     style="width: {{ $section['percent'] }}%"></div>
            </div>
            <p class="text-xs text-gray-500">
                {{ $section['filled'] }}/{{ $section['total'] }} fields
                &mdash;
                <span class="{{ $section['complete'] ? 'text-green-600' : 'text-amber-600' }} font-semibold">
                    {{ $section['complete'] ? 'Complete' : 'Incomplete' }}
                </span>
            </p>
        </a>
        @endforeach
    </div>
</div>

{{-- Services & Social Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    {{-- Services --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Services Offered</h3>
            <a href="{{ route('partner.business.edit') }}" class="text-xs text-[#ff0808] font-semibold hover:underline">Edit</a>
        </div>
        @php $services = is_array($partner?->services) ? $partner->services : []; @endphp
        @if(count($services))
            <div class="flex flex-wrap gap-2">
                @foreach($services as $service)
                    <span class="px-3 py-1 bg-red-50 text-[#ff0808] text-xs font-semibold rounded-md">
                        {{ $service }}
                    </span>
                @endforeach
            </div>
        @else
            <p class="text-xs text-gray-400">No services added yet.
                <a href="{{ route('partner.business.edit') }}" class="text-[#ff0808] font-semibold">Add services →</a>
            </p>
        @endif
    </div>

    {{-- Social Media --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Social Media</h3>
            <a href="{{ route('partner.social.edit') }}" class="text-xs text-[#ff0808] font-semibold hover:underline">Edit</a>
        </div>
        <div class="space-y-2">
            @foreach([
                ['field' => 'facebook_url',  'icon' => 'fab fa-facebook',  'color' => 'text-blue-600',  'label' => 'Facebook'],
                ['field' => 'instagram_url', 'icon' => 'fab fa-instagram', 'color' => 'text-pink-600',  'label' => 'Instagram'],
                ['field' => 'twitter_url',   'icon' => 'fab fa-twitter',   'color' => 'text-sky-500',   'label' => 'Twitter / X'],
                ['field' => 'linkedin_url',  'icon' => 'fab fa-linkedin',  'color' => 'text-blue-700',  'label' => 'LinkedIn'],
                ['field' => 'youtube_url',   'icon' => 'fab fa-youtube',   'color' => 'text-red-600',   'label' => 'YouTube'],
                ['field' => 'tiktok_url',    'icon' => 'fab fa-tiktok',    'color' => 'text-gray-900',  'label' => 'TikTok'],
            ] as $social)
                @if($partner?->{$social['field']})
                    <a href="{{ $partner->{$social['field']} }}" target="_blank"
                       class="flex items-center gap-2 text-xs text-gray-700 hover:text-gray-900">
                        <i class="{{ $social['icon'] }} {{ $social['color'] }} w-4 text-center"></i>
                        <span>{{ $social['label'] }}</span>
                        <i class="fas fa-external-link-alt text-gray-300 text-[10px] ml-auto"></i>
                    </a>
                @endif
            @endforeach
            @if(!$partner?->facebook_url && !$partner?->instagram_url && !$partner?->linkedin_url)
                <p class="text-xs text-gray-400">No social profiles added.
                    <a href="{{ route('partner.social.edit') }}" class="text-[#ff0808] font-semibold">Add now →</a>
                </p>
            @endif
        </div>
    </div>

</div>

@endsection
