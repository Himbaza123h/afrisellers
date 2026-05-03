@extends('layouts.home')
@section('page-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('partner.branding.show') }}" class="hover:text-gray-600">Branding</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Edit</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Edit Branding & Content</h1>
    </div>
    <a href="{{ route('partner.branding.show') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('partner.branding.update') }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider pb-2 border-b border-gray-100">Content</h2>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Short Description
                <span class="text-gray-400 font-normal">(max 300 chars)</span>
            </label>
            <input type="text" name="short_description"
                   value="{{ old('short_description', $partner?->short_description) }}"
                   maxlength="300"
                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                   placeholder="One-line description of your company">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Full Description</label>
            <textarea name="full_description" rows="6"
                      class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent resize-none"
                      placeholder="Full company description, mission, values...">{{ old('full_description', $partner?->full_description) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Promotional Video URL
                <span class="text-gray-400 font-normal">(optional)</span>
            </label>
            <input type="url" name="promo_video_url"
                   value="{{ old('promo_video_url', $partner?->promo_video_url) }}"
                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                   placeholder="https://youtube.com/watch?v=...">
        </div>
    </div>

    <div class="mt-4 flex items-center justify-end gap-3">
        <a href="{{ route('partner.branding.show') }}"
           class="px-5 py-2.5 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</a>
        <button type="submit"
                class="px-5 py-2.5 text-xs font-bold text-white bg-[#ff0808] rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </div>
</form>
@endsection
