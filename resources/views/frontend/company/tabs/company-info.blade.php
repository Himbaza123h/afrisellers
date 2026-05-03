@extends('layouts.app')

@section('title', $profile->company_name ?? $profile->user->name . ' — Company Info')

@section('content')

<div class="min-h-screen bg-gray-50 py-5 px-3 sm:px-6">
<div class="max-w-3xl mx-auto space-y-4">

    {{-- ── Header card ─────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4 flex items-center gap-4">
        @if($profile->logo_url ?? $profile->user->avatar ?? null)
            <img src="{{ $profile->logo_url ?? $profile->user->avatar }}"
                 alt="{{ $profile->company_name }}"
                 class="w-14 h-14 rounded-xl object-contain border border-gray-100 p-1 flex-shrink-0">
        @else
            <div class="w-14 h-14 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-building text-[#ff0808] text-xl"></i>
            </div>
        @endif
        <div class="min-w-0">
            <h1 class="text-base font-black text-gray-900 truncate">
                {{ $profile->company_name ?? $profile->user->name }}
            </h1>
            @if($profile->trading_name)
                <p class="text-xs text-gray-400 mt-0.5">
                    Trading as <span class="font-semibold text-gray-600">{{ $profile->trading_name }}</span>
                </p>
            @endif
            <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold rounded-full">
                <i class="fas fa-check-circle text-[9px]"></i> Verified Company
            </span>
        </div>
    </div>

    {{-- ── Company Details ──────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Section title --}}
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
            <div class="w-7 h-7 bg-red-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-id-card text-[#ff0808] text-xs"></i>
            </div>
            <h2 class="text-sm font-black text-gray-800">Company Details</h2>
        </div>

        <div class="divide-y divide-gray-50">

            {{-- Registration Number --}}
            @if($profile->registration_number)
            <div class="flex items-center gap-4 px-5 py-4">
                <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-id-badge text-indigo-600 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Registration Number</p>
                    <p class="text-sm font-bold text-gray-900">{{ $profile->registration_number }}</p>
                </div>
            </div>
            @endif

            {{-- Partner / Business Type --}}
            @if($profile->partner_type)
            <div class="flex items-center gap-4 px-5 py-4">
                <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-handshake text-[#ff0808] text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Partnership Type</p>
                    <span class="inline-block px-2.5 py-0.5 bg-red-50 text-[#ff0808] text-xs font-bold rounded-lg border border-red-100">
                        {{ $profile->partner_type }}
                    </span>
                </div>
            </div>
            @endif

            {{-- Year Established --}}
            @if($profile->established)
            <div class="flex items-center gap-4 px-5 py-4">
                <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-alt text-amber-500 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Year Established</p>
                    <p class="text-sm font-bold text-gray-900">
                        {{ $profile->established }}
                        <span class="text-xs font-normal text-gray-400 ml-1">
                            ({{ date('Y') - $profile->established }}+ years in business)
                        </span>
                    </p>
                </div>
            </div>
            @endif

            {{-- Country --}}
            @if($profile->country)
            <div class="flex items-center gap-4 px-5 py-4">
                <div class="w-8 h-8 bg-teal-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-globe text-teal-600 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Country of Registration</p>
                    <p class="text-sm font-bold text-gray-900">{{ $profile->country }}</p>
                </div>
            </div>
            @endif

            {{-- Physical Address --}}
            @if($profile->physical_address)
            <div class="flex items-start gap-4 px-5 py-4">
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas fa-map-marker-alt text-orange-500 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Physical Address</p>
                    <p class="text-sm font-semibold text-gray-900 leading-relaxed">{{ $profile->physical_address }}</p>
                </div>
            </div>
            @endif

            {{-- Website --}}
            @if($profile->website_url)
            <div class="flex items-center gap-4 px-5 py-4">
                <div class="w-8 h-8 bg-cyan-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-globe text-cyan-600 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-0.5">Official Website</p>
                    <a href="{{ $profile->website_url }}" target="_blank" rel="noopener noreferrer"
                       class="text-sm font-bold text-[#ff0808] hover:underline flex items-center gap-1 truncate">
                        {{ parse_url($profile->website_url, PHP_URL_HOST) ?? $profile->website_url }}
                        <i class="fas fa-external-link-alt text-[9px]"></i>
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- ── Empty state — if nothing is filled ─────────────── --}}
    @php
        $hasAny = $profile->registration_number || $profile->partner_type
               || $profile->established || $profile->country
               || $profile->physical_address || $profile->website_url;
    @endphp

    @if(!$hasAny)
    <div class="bg-white rounded-2xl border border-dashed border-gray-200 py-12 text-center">
        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-building text-gray-300 text-xl"></i>
        </div>
        <p class="text-sm font-semibold text-gray-400">No company information added yet.</p>
    </div>
    @endif

</div>
</div>

@endsection
