@extends('layouts.home')
@section('page-content')
<div class="max-w-2xl mx-auto px-4 py-5">
    <div class="mb-5">
        <div class="flex items-center gap-3 mb-1">
            <a href="{{ route('admin.departments.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600 text-sm"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-900">New Department</h1>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-5 rounded-lg">
            <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.departments.store') }}" method="POST" class="space-y-5">
        @csrf
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent @error('name') border-red-500 @enderror"
                       placeholder="e.g. Engineering, Marketing">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                          placeholder="Optional description">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Color</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="color" value="{{ old('color', '#6366f1') }}"
                           class="h-9 w-16 rounded-lg border border-gray-300 cursor-pointer">
                    <span class="text-xs text-gray-500">Used to visually identify this department in the admin panel</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                       class="w-4 h-4 text-[#ff0808] rounded border-gray-300 focus:ring-[#ff0808]">
                <label for="is_active" class="text-xs font-semibold text-gray-700">Active</label>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4 pt-2">
            <a href="{{ route('admin.departments.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-[#ff0808] text-white text-sm font-semibold rounded-lg hover:bg-red-700 shadow-sm">
                <i class="fas fa-save mr-2"></i>Create Department
            </button>
        </div>
    </form>
</div>
@endsection
