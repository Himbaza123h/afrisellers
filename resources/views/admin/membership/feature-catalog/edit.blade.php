@extends('layouts.home')

@section('page-content')
    <div class="space-y-4 max-w-3xl">
        <div class="flex gap-3 items-center">
            <a href="{{ route('admin.memberships.feature-catalog.index') }}" class="p-2 rounded-lg hover:bg-gray-100">
                <i class="text-gray-600 fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Edit feature</h1>
                <p class="text-xs text-gray-500">{{ $feature->feature_key }}</p>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg border border-gray-200 shadow-sm">
            <form action="{{ route('admin.memberships.feature-catalog.update', $feature) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $feature->name) }}" required
                        class="w-full px-3 py-2 border rounded-lg text-sm @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="3" placeholder="Optional"
                        class="w-full px-3 py-2 border rounded-lg text-sm @error('description') border-red-500 @enderror">{{ old('description', $feature->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Value type *</label>
                    <p class="text-[11px] text-gray-500 mb-1">Controls validation when editing plan feature values for this catalog row.</p>
                    <select name="value_type"
                        class="px-3 py-2 w-full text-sm rounded-lg border @error('value_type') border-red-500 @enderror">
                        @foreach(\App\Models\Feature::VALUE_TYPES as $vt)
                            <option value="{{ $vt }}" @selected(old('value_type', $feature->value_type ?: $feature->resolvedValueType()) === $vt)>{{ str_replace('_', ' ', $vt) }}</option>
                        @endforeach
                    </select>
                    @error('value_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div style="display: none">
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-700">Slug *</label>
                        <input type="text" name="slug" value="{{ old('slug', $feature->slug) }}" required
                            class="w-full px-3 py-2 border rounded-lg text-sm @error('slug') border-red-500 @enderror">
                        @error('slug')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-1 text-xs font-medium text-gray-700">Feature key *</label>
                        <input type="text" name="feature_key" value="{{ old('feature_key', $feature->feature_key) }}"
                            required
                            class="w-full px-3 py-2 border rounded-lg text-sm font-mono @error('feature_key') border-red-500 @enderror">
                        @error('feature_key')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-amber-600">Changing the key will break checks that still use the old
                            string until plans are updated.</p>
                    </div>
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700">Status *</label>
                    <select name="status" class="px-3 py-2 w-full text-sm rounded-lg border @error('status') border-red-500 @enderror">
                        <option value="active" @selected(old('status', $feature->status) === 'active')>active</option>
                        <option value="inactive" @selected(old('status', $feature->status) === 'inactive')>inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <div class="flex gap-2 items-center">
                        <input type="hidden" name="is_supported" value="0">
                        <input type="checkbox" name="is_supported" id="is_supported" value="1"
                            class="rounded border-gray-300 @error('is_supported') ring-2 ring-red-500 @enderror" @checked(old('is_supported', $feature->is_supported))>
                        <label for="is_supported" class="text-sm text-gray-700">Supported</label>
                    </div>
                    @error('is_supported')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="pt-2">
                    <button type="submit"
                        class="px-4 py-2 bg-[#ff0808] hover:bg-[#e60707] text-white rounded-lg text-sm font-medium">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
