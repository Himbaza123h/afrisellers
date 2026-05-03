@extends('layouts.home')
@section('page-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('partner.social.show') }}" class="hover:text-gray-600">Social Media</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Edit</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Edit Social Media</h1>
    </div>
    <a href="{{ route('partner.social.show') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<form action="{{ route('partner.social.update') }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-5 pb-2 border-b border-gray-100">Social Profiles</h2>
        <div class="space-y-4">
            @foreach([
                ['name' => 'facebook_url',  'icon' => 'fab fa-facebook',  'color' => 'text-blue-600',  'label' => 'Facebook Page URL',      'placeholder' => 'https://facebook.com/yourpage'],
                ['name' => 'instagram_url', 'icon' => 'fab fa-instagram', 'color' => 'text-pink-600',  'label' => 'Instagram Profile URL',   'placeholder' => 'https://instagram.com/yourprofile'],
                ['name' => 'twitter_url',   'icon' => 'fab fa-twitter',   'color' => 'text-sky-500',   'label' => 'Twitter (X) Profile URL', 'placeholder' => 'https://twitter.com/yourhandle'],
                ['name' => 'linkedin_url',  'icon' => 'fab fa-linkedin',  'color' => 'text-blue-700',  'label' => 'LinkedIn Company Page',   'placeholder' => 'https://linkedin.com/company/yourco'],
                ['name' => 'youtube_url',   'icon' => 'fab fa-youtube',   'color' => 'text-red-600',   'label' => 'YouTube Channel',         'placeholder' => 'https://youtube.com/@yourchannel'],
                ['name' => 'tiktok_url',    'icon' => 'fab fa-tiktok',    'color' => 'text-gray-900',  'label' => 'TikTok',                  'placeholder' => 'https://tiktok.com/@yourhandle'],
            ] as $s)
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    <i class="{{ $s['icon'] }} {{ $s['color'] }} mr-1"></i> {{ $s['label'] }}
                </label>
                <input type="url" name="{{ $s['name'] }}"
                       value="{{ old($s['name'], $partner?->{$s['name']}) }}"
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                       placeholder="{{ $s['placeholder'] }}">
            </div>
            @endforeach
        </div>
    </div>
    <div class="mt-4 flex items-center justify-end gap-3">
        <a href="{{ route('partner.social.show') }}"
           class="px-5 py-2.5 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</a>
        <button type="submit"
                class="px-5 py-2.5 text-xs font-bold text-white bg-[#ff0808] rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </div>
</form>
@endsection
