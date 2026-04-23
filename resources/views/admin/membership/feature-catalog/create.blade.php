@extends('layouts.home')

@section('page-content')
<div class="space-y-4 max-w-3xl">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.memberships.feature-catalog.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">New feature definition</h1>
            <p class="text-xs text-gray-500">Human-readable name, slug, and stable <code>feature_key</code> for code checks</p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <form action="{{ route('admin.memberships.feature-catalog.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border rounded-lg text-sm @error('name') border-red-500 @enderror">
                @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" placeholder="Optional — shown internally and in admin UIs" class="w-full px-3 py-2 border rounded-lg text-sm @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Slug *</label>
                <input type="text" name="slug" value="{{ old('slug') }}" required class="w-full px-3 py-2 border rounded-lg text-sm @error('slug') border-red-500 @enderror">
                @error('slug')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Feature key *</label>
                <input type="text" name="feature_key" value="{{ old('feature_key') }}" required placeholder="e.g. has_rfq_access" class="w-full px-3 py-2 border rounded-lg text-sm font-mono @error('feature_key') border-red-500 @enderror">
                @error('feature_key')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Value type *</label>
                <p class="text-[11px] text-gray-500 mb-1">How plan assignments store and validate this feature (boolean flags are stored as true when added).</p>
                <select name="value_type" class="w-full px-3 py-2 border rounded-lg text-sm @error('value_type') border-red-500 @enderror">
                    @foreach(\App\Models\Feature::VALUE_TYPES as $vt)
                        <option value="{{ $vt }}" @selected(old('value_type', 'text') === $vt)>{{ str_replace('_', ' ', $vt) }}</option>
                    @endforeach
                </select>
                @error('value_type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status *</label>
                <select name="status" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="active" @selected(old('status', 'active') === 'active')>active</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>inactive</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_supported" value="0">
                <input type="checkbox" name="is_supported" id="is_supported" value="1" class="rounded border-gray-300" @checked(old('is_supported', true))>
                <label for="is_supported" class="text-sm text-gray-700">Supported (available for new plan assignments)</label>
            </div>
            <div class="pt-2">
                <button type="submit" class="px-4 py-2 bg-[#ff0808] hover:bg-[#e60707] text-white rounded-lg text-sm font-medium">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
