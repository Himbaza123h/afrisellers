@extends('layouts.home')

@section('page-content')
<div class="max-w-6xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.partners.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-arrow-left text-gray-600 text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $partner->name }}</h1>
            <p class="text-xs text-gray-500">Partner details</p>
        </div>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('admin.partners.edit', $partner) }}"
               class="px-3 py-2 bg-[#ff0808] text-white text-sm font-bold rounded-lg hover:bg-red-700">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">

        <div class="flex items-center gap-5">
            @if($partner->logo_url)
                <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}"
                     class="h-16 w-auto max-w-[180px] object-contain">
            @else
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                </div>
            @endif
            <div>
                <h2 class="text-lg font-black text-gray-900">{{ $partner->name }}</h2>
                @if($partner->partner_type)
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full border border-blue-200">
                        {{ $partner->partner_type }}
                    </span>
                @endif
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold
                    {{ $partner->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $partner->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Industry</p>
                <p class="text-gray-900">{{ $partner->industry ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Website</p>
                @if($partner->website_url)
                    <a href="{{ $partner->website_url }}" target="_blank"
                       class="text-blue-600 hover:underline break-all">{{ $partner->website_url }}</a>
                @else
                    <p class="text-gray-400">—</p>
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Sort Order</p>
                <p class="text-gray-900">{{ $partner->sort_order }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Added</p>
                <p class="text-gray-900">{{ $partner->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        @if($partner->description)
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Description</p>
                <div class="prose prose-sm max-w-none text-gray-700 border border-gray-100 rounded-lg p-4 bg-gray-50">
                    {!! $partner->description !!}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
