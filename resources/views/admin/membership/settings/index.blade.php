@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex gap-4 items-center">
        <a href="{{ route('admin.memberships.plans.index') }}" class="p-2 rounded-lg hover:bg-gray-100">
            <i class="text-gray-600 fas fa-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Membership Settings</h1>
            <p class="mt-1 text-sm text-gray-500">Configure global membership system settings</p>
        </div>
    </div>

    @if(session('success'))
        <div class="flex gap-3 items-start p-4 bg-green-50 rounded-lg border border-green-200">
            <i class="mt-0.5 text-green-600 fas fa-check-circle"></i>
            <p class="flex-1 text-sm font-medium text-green-900">{{ session('success') }}</p>
        </div>
    @endif

    <form action="{{ route('admin.memberships.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Trial Settings -->
        @if($groupedSettings['trial']->count() > 0)
            <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <h3 class="flex gap-2 items-center mb-4 text-lg font-semibold text-gray-900">
                    <i class="text-purple-600 fas fa-gift"></i>
                    Trial Period Settings
                </h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach($groupedSettings['trial'] as $setting)
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            @if($setting->type === 'boolean')
                                <select name="settings[{{ $setting->id }}][value]" class="px-4 py-2.5 w-full rounded-lg border border-gray-300">
                                    <option value="true" {{ $setting->value === 'true' ? 'selected' : '' }}>Yes</option>
                                    <option value="false" {{ $setting->value === 'false' ? 'selected' : '' }}>No</option>
                                </select>
                            @elseif($setting->type === 'integer')
                                <input type="number" name="settings[{{ $setting->id }}][value]" value="{{ $setting->value }}" class="px-4 py-2.5 w-full rounded-lg border border-gray-300">
                            @elseif($setting->type === 'decimal')
                                <input type="number" step="0.01" name="settings[{{ $setting->id }}][value]" value="{{ $setting->value }}" class="px-4 py-2.5 w-full rounded-lg border border-gray-300">
                            @else
                                <input type="text" name="settings[{{ $setting->id }}][value]" value="{{ $setting->value }}" class="px-4 py-2.5 w-full rounded-lg border border-gray-300">
                            @endif
                            <input type="hidden" name="settings[{{ $setting->id }}][key]" value="{{ $setting->key }}">
                            <input type="hidden" name="settings[{{ $setting->id }}][type]" value="{{ $setting->type }}">
                            @if($setting->description)
                                <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Limit Settings -->
        @if($groupedSettings['limits']->count() > 0)
            <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <h3 class="flex gap-2 items-center mb-4 text-lg font-semibold text-gray-900">
                    <i class="text-blue-600 fas fa-sliders-h"></i>
                    Limits & Restrictions
                </h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach($groupedSettings['limits'] as $setting)
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            @if($setting->type === 'integer')
                                <input type="number" name="settings[{{ $setting->id }}][value]" value="{{ $setting->value }}" class="px-4 py-2.5 w-full rounded-lg border border-gray-300">
                            @else
                                <input type="text" name="settings[{{ $setting->id }}][value]" value="{{ $setting->value }}" class="px-4 py-2.5 w-full rounded-lg border border-gray-300">
                            @endif
                            <input type="hidden" name="settings[{{ $setting->id }}][key]" value="{{ $setting->key }}">
                            <input type="hidden" name="settings[{{ $setting->id }}][type]" value="{{ $setting->type }}">
                            @if($setting->description)
                                <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Pricing Settings -->
        @if($groupedSettings['pricing']->count() > 0)
            <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <h3 class="flex gap-2 items-center mb-4 text-lg font-semibold text-gray-900">
                    <i class="text-green-600 fas fa-dollar-sign"></i>
                    Pricing Settings
                </h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach($groupedSettings['pricing'] as $setting)
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 text-gray-500 -translate-y-1/2">$</span>
                                <input type="number" step="0.01" name="settings[{{ $setting->id }}][value]" value="{{ $setting->value }}" class="py-2.5 pr-4 pl-8 w-full rounded-lg border border-gray-300">
                            </div>
                            <input type="hidden" name="settings[{{ $setting->id }}][key]" value="{{ $setting->key }}">
                            <input type="hidden" name="settings[{{ $setting->id }}][type]" value="{{ $setting->type }}">
                            @if($setting->description)
                                <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Feature Flags -->
        @if($groupedSettings['features']->count() > 0)
            <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <h3 class="flex gap-2 items-center mb-4 text-lg font-semibold text-gray-900">
                    <i class="text-orange-600 fas fa-toggle-on"></i>
                    Feature Flags
                </h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach($groupedSettings['features'] as $setting)
                        <div class="flex gap-3 items-center">
                            <input type="checkbox" name="settings[{{ $setting->id }}][value]" value="true" {{ $setting->value === 'true' ? 'checked' : '' }} class="w-5 h-5 text-red-600 rounded border-gray-300">
                            <div class="flex-1">
                                <label class="text-sm font-medium text-gray-900">
                                    {{ ucwords(str_replace(['has_', '_'], ['', ' '], $setting->key)) }}
                                </label>
                                @if($setting->description)
                                    <p class="text-xs text-gray-500">{{ $setting->description }}</p>
                                @endif
                            </div>
                            <input type="hidden" name="settings[{{ $setting->id }}][key]" value="{{ $setting->key }}">
                            <input type="hidden" name="settings[{{ $setting->id }}][type]" value="{{ $setting->type }}">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- All Settings Table -->
        <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-6 border-b">
                <h3 class="flex gap-2 items-center text-lg font-semibold text-gray-900">
                    <i class="text-gray-600 fas fa-list"></i>
                    All Settings
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Key</th>
                            <th class="px-6 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Value</th>
                            <th class="px-6 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Type</th>
                            <th class="px-6 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($settings as $setting)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <code class="font-mono text-xs text-blue-600">{{ $setting->key }}</code>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900">{{ $setting->value }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded">{{ $setting->type }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">{{ $setting->description ?? '-' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 justify-end items-center">
            <a href="{{ route('admin.memberships.plans.index') }}" class="px-6 py-2.5 font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] font-medium">
                <i class="mr-2 fas fa-save"></i>Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
