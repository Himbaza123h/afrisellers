@extends('layouts.home')

@section('page-content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('agent.settings.index') }}"
           class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">General Settings</h1>
            <p class="text-xs text-gray-500 mt-0.5">Timezone, language and display preferences</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('agent.settings.update-general') }}" method="POST" class="space-y-5">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">

            {{-- Timezone --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Timezone <span class="text-red-500">*</span>
                </label>
                <select name="timezone"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach(timezone_identifiers_list() as $tz)
                        <option value="{{ $tz }}" {{ $settings->timezone === $tz ? 'selected' : '' }}>
                            {{ $tz }}
                        </option>
                    @endforeach
                </select>
                @error('timezone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Language --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Language <span class="text-red-500">*</span>
                </label>
                <select name="language"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach(['en'=>'English','fr'=>'French','es'=>'Spanish','ar'=>'Arabic','sw'=>'Swahili'] as $val => $label)
                        <option value="{{ $val }}" {{ $settings->language === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('language')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Currency --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Display Currency <span class="text-red-500">*</span>
                </label>
                <select name="currency"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach(['USD'=>'USD — US Dollar','EUR'=>'EUR — Euro','GBP'=>'GBP — British Pound','KES'=>'KES — Kenyan Shilling','NGN'=>'NGN — Nigerian Naira','GHS'=>'GHS — Ghanaian Cedi','ZAR'=>'ZAR — South African Rand','RWF'=>'RWF — Rwandan Franc'] as $val => $label)
                        <option value="{{ $val }}" {{ $settings->currency === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('currency')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Date Format --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Date Format <span class="text-red-500">*</span>
                </label>
                <select name="date_format"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach(['M d, Y'=>'Apr 05, 2026','d/m/Y'=>'05/04/2026','m/d/Y'=>'04/05/2026','Y-m-d'=>'2026-04-05'] as $val => $preview)
                        <option value="{{ $val }}" {{ $settings->date_format === $val ? 'selected' : '' }}>
                            {{ $preview }}
                        </option>
                    @endforeach
                </select>
                @error('date_format')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('agent.settings.index') }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
