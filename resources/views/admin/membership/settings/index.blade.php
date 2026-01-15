@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.memberships.plans.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Membership Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Configure global membership system settings</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
        </div>
    @endif>

    <form action="{{ route('admin.memberships.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Trial Settings -->
        @if($groupedSettings['trial']->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-gift text-purple-600"></i>
                    Trial Period Settings
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($groupedSettings['trial'] as $setting)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            @if($setting->type === 'boolean')
                                <select name="settings[{{ $setting->id }}][value]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                                    <option value="true" {{ $setting->value === 'true' ? 'selected' : '' }}>Yes</option>
                                    <option value="false" {{ $setting->value === 'false' ? 'selected' : '' }}>No</option>
                                </select>
                            @elseif($setting->type === 'integer')
                                <input type="number" name="settings[{{ $setting->id }}][value]" value="{{ $setting->value }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                            @elseif($setting->type === 'decimal')
                                <input type="number" step="0.01" name="settings[{{ $setting->id }}][value]" value="{{ $setting->value }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                            @else
                                <input type="text" name="settings[{{ $setting->id }}][value]" value="{{ $setting->value }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                            @endif
                            <input type="hidden" name="settings[{{ $setting->id }}][key]" value="{{ $setting->key }}">
                            <input type="hidden" name="settings[{{ $setting->id }}][type]" value="{{ $setting->type }}">
                            @if($setting->description)
                                <p class="text-xs text-gray-500 mt-1">{{ $setting->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Limit Settings -->
        @if($groupedSettings['limits']->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-sliders-h text-blue-600"></i>
                    Limits & Restrictions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($groupedSettings['limits'] as $setting)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            @if($setting->type === 'integer')
                                <input type="number" name="settings[{{ $setting->id }}][value]" value="{{ $setting->value }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                            @else
                                <input type="text" name="settings[{{ $setting->id }}][value]" value="{{ $setting->value }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                            @endif
                            <input type="hidden" name="settings[{{ $setting->id }}][key]" value="{{ $setting->key }}">
                            <input type="hidden" name="settings[{{ $setting->id }}][type]" value="{{ $setting->type }}">
                            @if($setting->description)
                                <p class="text-xs text-gray-500 mt-1">{{ $setting->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Pricing Settings -->
        @if($groupedSettings['pricing']->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                    Pricing Settings
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($groupedSettings['pricing'] as $setting)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                <input type="number" step="0.01" name="settings[{{ $setting->id }}][value]" value="{{ $setting->value }}" class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg">
                            </div>
                            <input type="hidden" name="settings[{{ $setting->id }}][key]" value="{{ $setting->key }}">
                            <input type="hidden" name="settings[{{ $setting->id }}][type]" value="{{ $setting->type }}">
                            @if($setting->description)
                                <p class="text-xs text-gray-500 mt-1">{{ $setting->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Feature Flags -->
        @if($groupedSettings['features']->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-toggle-on text-orange-600"></i>
                    Feature Flags
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($groupedSettings['features'] as $setting)
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="settings[{{ $setting->id }}][value]" value="true" {{ $setting->value === 'true' ? 'checked' : '' }} class="w-5 h-5 text-red-600 border-gray-300 rounded">
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
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-list text-gray-600"></i>
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
                                    <code class="text-xs font-mono text-blue-600">{{ $setting->key }}</code>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900">{{ $setting->value }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ $setting->type }}</span>
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
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.memberships.plans.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] font-medium">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
