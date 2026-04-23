@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- ── Header ── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.showrooms.index') }}"
               class="p-1.5 rounded hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-gray-900 uppercase sm:text-2xl lg:text-lg">Showroom Details</h1>
                <p class="mt-0.5 text-xs text-gray-500">{{ $showroom->showroom_number }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            {{-- Verify / Unverify --}}
            @if($showroom->is_verified)
                <form action="{{ route('admin.showrooms.unverify', $showroom) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-yellow-700 bg-yellow-100 border border-yellow-200 rounded-lg hover:bg-yellow-200 transition-colors">
                        <i class="fas fa-shield-alt"></i> Revoke Verification
                    </button>
                </form>
            @else
                <form action="{{ route('admin.showrooms.verify', $showroom) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-blue-700 bg-blue-100 border border-blue-200 rounded-lg hover:bg-blue-200 transition-colors">
                        <i class="fas fa-shield-alt"></i> Verify
                    </button>
                </form>
            @endif

            {{-- Feature / Unfeature --}}
            <form action="{{ route('admin.showrooms.feature', $showroom) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold
                               {{ $showroom->is_featured ? 'text-purple-700 bg-purple-100 border-purple-200 hover:bg-purple-200' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50' }}
                               border rounded-lg transition-colors">
                    <i class="fas fa-star"></i> {{ $showroom->is_featured ? 'Unfeature' : 'Feature' }}
                </button>
            </form>

            {{-- Activate / Suspend --}}
            @if($showroom->status === 'suspended')
                <form action="{{ route('admin.showrooms.activate', $showroom) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-green-700 bg-green-100 border border-green-200 rounded-lg hover:bg-green-200 transition-colors">
                        <i class="fas fa-check-circle"></i> Activate
                    </button>
                </form>
            @else
                <form action="{{ route('admin.showrooms.suspend', $showroom) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Suspend this showroom?')"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-orange-700 bg-orange-100 border border-orange-200 rounded-lg hover:bg-orange-200 transition-colors">
                        <i class="fas fa-ban"></i> Suspend
                    </button>
                </form>
            @endif

            {{-- Delete --}}
            <form action="{{ route('admin.showrooms.destroy', $showroom) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Permanently delete this showroom?')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-red-600 border border-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-600"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- ── Body Grid ── --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- ── Left / Main ── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Banner / Cover Image --}}
            @php
                $coverRaw = $showroom->cover_image ?? $showroom->banner_image ?? $showroom->image ?? null;
                $coverUrl = $coverRaw
                    ? (str_starts_with($coverRaw, 'http') ? $coverRaw : asset($coverRaw))
                    : null;
            @endphp
            @if($coverUrl)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <img src="{{ $coverUrl }}"
                         alt="{{ $showroom->name }}"
                         class="w-full h-48 object-cover sm:h-64">
                </div>
            @endif

            {{-- Basic Info --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                    <div class="w-8 h-8 bg-[#ff0808] rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-store text-white text-sm"></i>
                    </div>
                    <h2 class="text-base font-bold text-gray-900">Showroom Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-y-4 gap-x-6 sm:grid-cols-2 md:grid-cols-3">
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Name</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $showroom->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Showroom #</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $showroom->showroom_number ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Business Type</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $showroom->business_type ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Email</p>
                            <p class="mt-1 text-sm text-gray-900 break-all">{{ $showroom->email ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Phone</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $showroom->phone ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Website</p>
                            @if($showroom->website)
                                <a href="{{ $showroom->website }}" target="_blank"
                                   class="mt-1 text-sm text-[#ff0808] hover:underline truncate block">
                                    {{ $showroom->website }}
                                </a>
                            @else
                                <p class="mt-1 text-sm text-gray-900">—</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Country</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $showroom->country->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">City</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $showroom->city ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Size (sqm)</p>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $showroom->showroom_size_sqm ? number_format($showroom->showroom_size_sqm) . ' m²' : '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Rating</p>
                            <p class="mt-1 text-sm text-gray-900 flex items-center gap-1">
                                @if($showroom->rating)
                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                    {{ number_format($showroom->rating, 1) }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Views</p>
                            <p class="mt-1 text-sm text-gray-900">{{ number_format($showroom->views_count ?? 0) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Inquiries</p>
                            <p class="mt-1 text-sm text-gray-900">{{ number_format($showroom->inquiries_count ?? 0) }}</p>
                        </div>
                    </div>

                    {{-- Address --}}
                    @if($showroom->address)
                        <div class="mt-5 pt-4 border-t border-gray-100">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Address</p>
                            <p class="text-sm text-gray-700">{{ $showroom->address }}</p>
                        </div>
                    @endif

                    {{-- Description --}}
                    @if($showroom->description)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Description</p>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $showroom->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Products --}}
            @if($showroom->products && $showroom->products->isNotEmpty())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-blue-600 text-sm"></i>
                            </div>
                            <h2 class="text-base font-bold text-gray-900">Products</h2>
                        </div>
                        <span class="text-xs font-semibold text-gray-400">{{ $showroom->products->count() }} total</span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach($showroom->products->take(12) as $product)
                                @php
                                    $primaryImg = $product->images?->firstWhere('is_primary', true)
                                        ?? $product->images?->first();
                                    $productImgRaw = $primaryImg?->image_url ?? null;
                                    $productImgUrl = $productImgRaw
                                        ? (str_starts_with($productImgRaw, 'http') ? $productImgRaw : asset($productImgRaw))
                                        : null;
                                @endphp
                                <a href="{{ route('admin.vendor.product.show', $product) }}"
                                   class="group block border border-gray-200 rounded-xl overflow-hidden hover:border-[#ff0808] transition-colors">
                                    <div class="aspect-square bg-gray-100 overflow-hidden">
                                        @if($productImgUrl)
                                            <img src="{{ $productImgUrl }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-image text-gray-300 text-2xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="px-2 py-2">
                                        <p class="text-xs font-semibold text-gray-900 truncate">{{ $product->name }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">
                                            @php
                                                $sc = match($product->status) {
                                                    'active'   => 'text-green-600',
                                                    'inactive' => 'text-red-500',
                                                    default    => 'text-yellow-600',
                                                };
                                            @endphp
                                            <span class="{{ $sc }}">{{ ucfirst($product->status) }}</span>
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        @if($showroom->products->count() > 12)
                            <p class="mt-3 text-xs text-gray-400 text-center">
                                + {{ $showroom->products->count() - 12 }} more products
                            </p>
                        @endif
                    </div>
                </div>
            @endif

        </div>{{-- end left --}}

        {{-- ── Sidebar ── --}}
        <div class="space-y-4">

            {{-- Status Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-bold text-gray-900">Status</h2>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Status badge --}}
                    <div class="flex flex-wrap gap-2">
                        @php
                            $statusStyle = match($showroom->status) {
                                'active'    => 'bg-green-100 text-green-800',
                                'pending'   => 'bg-yellow-100 text-yellow-800',
                                'suspended' => 'bg-red-100 text-red-800',
                                'inactive'  => 'bg-gray-100 text-gray-700',
                                default     => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusStyle }}">
                            {{ ucfirst($showroom->status) }}
                        </span>

                        @if($showroom->is_verified)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                <i class="fas fa-shield-alt mr-1"></i> Verified
                            </span>
                        @endif

                        @if($showroom->is_featured)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                <i class="fas fa-star mr-1"></i> Featured
                            </span>
                        @endif
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-gray-100 text-xs text-gray-500">
                        <p><span class="font-medium text-gray-600">Registered:</span> {{ $showroom->created_at->format('M d, Y') }}</p>
                        <p><span class="font-medium text-gray-600">Updated:</span> {{ $showroom->updated_at->format('M d, Y') }}</p>
                        <p><span class="font-medium text-gray-600">Products:</span> {{ $showroom->products?->count() ?? 0 }}</p>
                        <p><span class="font-medium text-gray-600">Views:</span> {{ number_format($showroom->views_count ?? 0) }}</p>
                        <p><span class="font-medium text-gray-600">Inquiries:</span> {{ number_format($showroom->inquiries_count ?? 0) }}</p>
                    </div>
                </div>
            </div>

            {{-- Owner Card --}}
            @if($showroom->user)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-bold text-gray-900">Owner</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-[#ff0808] rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($showroom->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $showroom->user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $showroom->user->email }}</p>
                            </div>
                        </div>
                        @if($showroom->user->businessProfile ?? null)
                            <a href="{{ route('admin.business-profile.show', $showroom->user->businessProfile) }}"
                               class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#ff0808] hover:underline">
                                <i class="fas fa-external-link-alt text-[10px]"></i> View Business Profile
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Stats Card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-bold text-gray-900">Quick Stats</h2>
                </div>
                <div class="p-4 grid grid-cols-2 gap-3">
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                        <p class="text-lg font-black text-blue-700">{{ number_format($showroom->views_count ?? 0) }}</p>
                        <p class="text-[10px] font-semibold text-blue-500 uppercase mt-0.5">Views</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 text-center">
                        <p class="text-lg font-black text-green-700">{{ number_format($showroom->inquiries_count ?? 0) }}</p>
                        <p class="text-[10px] font-semibold text-green-500 uppercase mt-0.5">Inquiries</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-3 text-center">
                        <p class="text-lg font-black text-yellow-700">
                            {{ $showroom->rating ? number_format($showroom->rating, 1) : '—' }}
                        </p>
                        <p class="text-[10px] font-semibold text-yellow-500 uppercase mt-0.5">Rating</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3 text-center">
                        <p class="text-lg font-black text-purple-700">{{ $showroom->products?->count() ?? 0 }}</p>
                        <p class="text-[10px] font-semibold text-purple-500 uppercase mt-0.5">Products</p>
                    </div>
                </div>
            </div>

        </div>{{-- end sidebar --}}
    </div>
</div>
@endsection
