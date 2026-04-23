@extends('layouts.home')

@section('page-content')

<!-- Header -->
<div class="mb-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-black text-gray-900 uppercase sm:text-2xl lg:text-lg">Product Details</h1>
            <p class="mt-1 text-xs text-gray-600 sm:text-sm">View product information and manage images</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.vendor.product.edit', $product) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                <i class="fas fa-pencil"></i> Edit Product
            </a>
            @php $businessProfile = $product->user->businessProfile ?? null; @endphp
            @if($businessProfile)
                <a href="{{ route('admin.business-profile.show', $businessProfile) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left"></i> Back to Business Profile
                </a>
            @else
                <a href="{{ route('admin.vendor.product.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left"></i> Back to Products
                </a>
            @endif
        </div>
    </div>
</div>

@if(session('success'))
    <div class="p-4 mb-4 bg-green-50 rounded-lg border border-green-300">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- ── Left / Main ── --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- ── Image Gallery ── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Image Gallery</h2>
                <span class="text-sm text-gray-500">{{ $product->images->count() }} image(s)</span>
            </div>
            <div class="p-6">
                @if($product->images->isNotEmpty())
                    @php
                        $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                        $mainImg      = $primaryImage->image_url ?? '';
                        $mainImgUrl   = str_starts_with($mainImg, 'http') ? $mainImg : ($mainImg ? asset($mainImg) : '');
                    @endphp
                    {{-- Main viewer --}}
                    <div class="mb-4 rounded-xl overflow-hidden bg-gray-100 aspect-video flex items-center justify-center border border-gray-200">
                        <img id="mainImage"
                             src="{{ $mainImgUrl }}"
                             alt="{{ $product->name }}"
                             class="max-h-full max-w-full object-contain transition-opacity duration-200">
                    </div>
                    {{-- Thumbnails --}}
                    <div class="grid grid-cols-5 gap-2 sm:grid-cols-6 lg:grid-cols-8" id="thumbnailGrid">
                        @foreach($product->images->sortBy('sort_order') as $image)
                            @php
                                $thumbUrl = str_starts_with($image->image_url, 'http') ? $image->image_url : asset($image->image_url);
                            @endphp
                            <div class="group relative cursor-pointer">
                                <div class="aspect-square rounded-lg overflow-hidden border-2 transition-all
                                            {{ $image->is_primary ? 'border-[#ff0808]' : 'border-transparent hover:border-gray-300' }}"
                                     onclick="setMainImage('{{ $thumbUrl }}', this)">
                                    <img src="{{ $thumbUrl }}"
                                         alt="Image {{ $loop->iteration }}"
                                         class="w-full h-full object-cover">
                                </div>
                                @if($image->is_primary)
                                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-[#ff0808] rounded-full flex items-center justify-center shadow">
                                        <i class="fas fa-star text-white" style="font-size:8px;"></i>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <i class="fas fa-images text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 font-medium">No images uploaded</p>
                        <p class="text-gray-400 text-sm mt-1">Edit the product to add images</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Video ── --}}
        @if($product->video_url)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Product Video</h2>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-600 text-xs font-semibold rounded-full">
                    <i class="fas fa-video text-[10px]"></i> Video
                </span>
            </div>
            <div class="p-6">
                <div class="rounded-xl overflow-hidden border border-gray-200 bg-black">
                    <video controls class="w-full max-h-80 object-contain bg-black" preload="metadata">
                        <source src="{{ str_starts_with($product->video_url, 'http') ? $product->video_url : Storage::url($product->video_url) }}" type="video/mp4">
                        <source src="{{ str_starts_with($product->video_url, 'http') ? $product->video_url : Storage::url($product->video_url) }}" type="video/webm">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
        @endif

        {{-- ── Product Information ── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Product Information</h2>
            </div>
            <div class="p-6 space-y-6">

                {{-- Grid of quick facts --}}
                <div class="grid grid-cols-2 gap-x-6 gap-y-4 md:grid-cols-3">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Product Name</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Category</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->productCategory->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Country</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->country->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Min Order Qty</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->min_order_quantity ?? 1 }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Negotiable</p>
                        <p class="mt-1">
                            @if($product->is_negotiable)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                    <i class="fas fa-check text-[9px]"></i> Yes
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-semibold rounded-full">No</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Views</p>
                        <p class="mt-1 text-sm text-gray-900">{{ number_format($product->views ?? 0) }}</p>
                    </div>
                    @if($product->video_url)
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Video</p>
                        <span class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                            <i class="fas fa-check text-[9px]"></i> Uploaded
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Short description --}}
                @if($product->short_description)
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Short Description</p>
                        <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 border border-gray-100">
                            {{ $product->short_description }}
                        </p>
                    </div>
                @endif

                {{-- Full description --}}
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Description</p>
                    <div class="prose prose-sm max-w-none text-gray-700 bg-gray-50 rounded-lg p-4 border border-gray-100 leading-relaxed rich-content">
                        {!! $product->description !!}
                    </div>
                </div>

                {{-- Specifications --}}
                @if($product->specifications)
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Specifications</p>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            @foreach($product->specifications as $key => $value)
                                <div class="flex divide-x divide-gray-200 {{ $loop->odd ? 'bg-gray-50' : 'bg-white' }}">
                                    <div class="w-1/3 px-4 py-2.5 text-xs font-semibold text-gray-600">{{ $key }}</div>
                                    <div class="flex-1 px-4 py-2.5 text-xs text-gray-900">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ── Sidebar ── --}}
    <div class="space-y-6">

        {{-- Status Card --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Status</h2>
            </div>
            <div class="p-6 space-y-4">
                @php
                    $statusStyle = match($product->status) {
                        'active'   => 'bg-green-100 text-green-800',
                        'inactive' => 'bg-red-100 text-red-800',
                        'draft'    => 'bg-yellow-100 text-yellow-800',
                        default    => 'bg-gray-100 text-gray-800',
                    };
                @endphp
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusStyle }}">
                        {{ ucfirst($product->status) }}
                    </span>
                    @if($product->is_admin_verified)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            <i class="fas fa-shield-alt mr-1"></i> Verified
                        </span>
                    @endif
                </div>
                <div class="space-y-1.5 pt-1 border-t border-gray-100">
                    <p class="text-xs text-gray-500">
                        <span class="font-medium text-gray-600">Added:</span>
                        {{ $product->created_at->format('M d, Y') }}
                    </p>
                    <p class="text-xs text-gray-500">
                        <span class="font-medium text-gray-600">Updated:</span>
                        {{ $product->updated_at->format('M d, Y') }}
                    </p>
                    <p class="text-xs text-gray-500">
                        <span class="font-medium text-gray-600">Images:</span>
                        {{ $product->images->count() }} / 4
                    </p>
                    <p class="text-xs text-gray-500">
                        <span class="font-medium text-gray-600">Video:</span>
                        {{ $product->video_url ? 'Yes' : 'None' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Vendor Card --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Vendor</h2>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-[#ff0808] rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($product->user->name ?? 'V', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $product->user->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $product->user->email ?? '' }}</p>
                    </div>
                </div>
                @if($product->user?->vendor)
                    <a href="{{ route('admin.vendors.show', $product->user->vendor) }}"
                       class="inline-flex items-center gap-2 text-xs font-semibold text-[#ff0808] hover:underline">
                        <i class="fas fa-external-link-alt"></i> View Vendor Profile
                    </a>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Actions</h2>
            </div>
            <div class="p-6 space-y-3">
                @if($product->status !== 'active')
                    <form action="{{ route('admin.vendor.product.approve', $product) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check-circle"></i> Approve Product
                        </button>
                    </form>
                @endif
                @if($product->status !== 'inactive')
                    <form action="{{ route('admin.vendor.product.reject', $product) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-times-circle"></i> Reject Product
                        </button>
                    </form>
                @endif
                <form action="{{ route('admin.vendor.product.destroy', $product) }}" method="POST"
                      onsubmit="return confirm('Permanently delete this product?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash"></i> Delete Product
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function setMainImage(url, el) {
    const main = document.getElementById('mainImage');
    main.classList.add('opacity-0');
    setTimeout(() => {
        main.src = url;
        main.classList.remove('opacity-0');
    }, 150);

    document.querySelectorAll('#thumbnailGrid .aspect-square').forEach(d => {
        d.classList.remove('border-[#ff0808]');
        d.classList.add('border-transparent', 'hover:border-gray-300');
    });
    el.classList.remove('border-transparent', 'hover:border-gray-300');
    el.classList.add('border-[#ff0808]');
}
</script>

<style>
.rich-content h1 { font-size: 1.25rem; font-weight: 700; margin: 0.75rem 0 0.5rem; }
.rich-content h2 { font-size: 1.1rem;  font-weight: 700; margin: 0.75rem 0 0.5rem; }
.rich-content h3 { font-size: 1rem;    font-weight: 600; margin: 0.5rem 0 0.35rem; }
.rich-content p  { margin-bottom: 0.5rem; }
.rich-content ul { list-style: disc;    padding-left: 1.25rem; margin-bottom: 0.5rem; }
.rich-content ol { list-style: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
.rich-content li { margin-bottom: 0.25rem; }
.rich-content strong { font-weight: 600; }
.rich-content em { font-style: italic; }
.rich-content u  { text-decoration: underline; }
.rich-content s  { text-decoration: line-through; }
.rich-content a  { color: #ff0808; text-decoration: underline; }
.rich-content blockquote {
    border-left: 3px solid #d1d5db;
    padding-left: 1rem;
    color: #6b7280;
    margin: 0.5rem 0;
    font-style: italic;
}
</style>
@endsection
