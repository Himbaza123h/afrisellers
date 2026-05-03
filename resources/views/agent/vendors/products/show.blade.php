@extends('layouts.home')

@section('page-content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('agent.vendors.products.index', $vendor->id) }}"
               class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $vendor->businessProfile?->business_name }}
                    @if($product->productCategory)
                        · {{ $product->productCategory->name }}
                    @endif
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('agent.vendors.edit', $vendor->id) }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            @if($vendor->account_status === 'active')
                <button type="button" onclick="switchToVendor({{ $vendor->id }})"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium shadow-sm">
                    <i class="fas fa-exchange-alt"></i> Switch to Vendor Dashboard
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── Left ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Images --}}
            @if($product->images->isNotEmpty())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                @php
                    $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                    $rawPrimary = $primary->getRawOriginal('image_url');
                    $primarySrc = str_starts_with($rawPrimary, 'http') ? $rawPrimary : asset('storage/' . $rawPrimary);
                @endphp

                {{-- Main image --}}
                <div class="aspect-video bg-gray-100 overflow-hidden">
                    <img id="mainImage" src="{{ $primarySrc }}" alt="{{ $primary->alt_text }}"
                         class="w-full h-full object-contain">
                </div>

                {{-- Thumbnails --}}
                @if($product->images->count() > 1)
                <div class="p-4 flex gap-2 overflow-x-auto">
                    @foreach($product->images as $img)
                        @php
                            $raw = $img->getRawOriginal('image_url');
                            $src = str_starts_with($raw, 'http') ? $raw : asset('storage/' . $raw);
                        @endphp
                        <button type="button" onclick="document.getElementById('mainImage').src='{{ $src }}'"
                                class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2
                                       {{ $img->is_primary ? 'border-[#ff0808]' : 'border-gray-200' }}
                                       hover:border-[#ff0808] transition-colors">
                            <img src="{{ $src }}" alt="{{ $img->alt_text }}"
                                 class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
                @endif
            </div>
            @else
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="aspect-video flex flex-col items-center justify-center text-gray-300">
                    <i class="fas fa-image text-5xl mb-3"></i>
                    <p class="text-sm">No images uploaded</p>
                </div>
            </div>
            @endif

            {{-- Video --}}
            @if($product->video_url)
                @php
                    $vRaw = $product->video_url;
                    $vSrc = str_starts_with($vRaw, 'http') ? $vRaw : asset('storage/' . $vRaw);
                @endphp
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-2">
                        <i class="fas fa-video text-gray-400"></i>
                        <h2 class="text-base font-bold text-gray-900">Product Video</h2>
                    </div>
                    <div class="p-4">
                        <video src="{{ $vSrc }}" controls
                               class="w-full max-h-72 rounded-lg bg-black object-contain"></video>
                    </div>
                </div>
            @endif

            {{-- Description --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-bold text-gray-900">Description</h2>
                </div>
                <div class="p-6">
                    @if($product->short_description)
                        <p class="text-sm text-gray-600 mb-4 pb-4 border-b border-gray-100">
                            {{ $product->short_description }}
                        </p>
                    @endif
                    @if($product->description)
                        <div class="prose prose-sm max-w-none text-gray-700">
                            {!! $product->description !!}
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">No description provided.</p>
                    @endif
                </div>
            </div>

        </div>

        {{-- ── Sidebar ── --}}
        <div class="space-y-6">

            {{-- Status badge --}}
            @php
                $sc = match($product->status) {
                    'active'   => 'bg-green-100 text-green-700 border-green-200',
                    'inactive' => 'bg-red-100 text-red-700 border-red-200',
                    default    => 'bg-gray-100 text-gray-600 border-gray-200',
                };
            @endphp
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-bold text-gray-900">Details</h2>
                </div>
                <div class="p-6 space-y-4">

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Status</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $sc }}">
                            <i class="fas fa-circle text-[6px]"></i>
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Category</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $product->productCategory?->name ?? '—' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Country of Origin</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $product->country?->name ?? '—' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Min. Order Qty</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $product->min_order_quantity ?? 1 }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Negotiable</span>
                        @if($product->is_negotiable)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                                <i class="fas fa-check text-[9px]"></i> Yes
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                <i class="fas fa-times text-[9px]"></i> No
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Images</span>
                        <span class="text-sm font-medium text-gray-900">{{ $product->images->count() }}</span>
                    </div>

                    <div class="pt-3 border-t border-gray-100 space-y-1">
                        <p class="text-xs text-gray-400">Created: {{ $product->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-400">Updated: {{ $product->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            {{-- Vendor card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-bold text-gray-900">Vendor</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#ff0808] rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr($vendor->businessProfile?->business_name ?? 'V', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ $vendor->businessProfile?->business_name }}
                            </p>
                            <p class="text-xs text-gray-400 truncate">{{ $vendor->user?->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('agent.vendors.show', $vendor->id) }}"
                       class="mt-4 w-full inline-flex justify-center items-center gap-2 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-50">
                        <i class="fas fa-store"></i> View Vendor
                    </a>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('agent.vendors.products.index', $vendor->id) }}"
                   class="flex-1 inline-flex justify-center items-center px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Back
                </a>
                <a href="{{ route('agent.vendors.products.edit', [$vendor->id, $product->id]) }}"
                   class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
