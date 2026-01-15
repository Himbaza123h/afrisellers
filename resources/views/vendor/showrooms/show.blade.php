@extends('layouts.home')

@section('page-content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('vendor.showrooms.index') }}"
           class="w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-lg font-black text-gray-900 uppercase lg:text-xl">{{ $showroom->name }}</h1>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="p-4 mb-6 bg-green-50 rounded-md border border-green-300">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Statistics -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Total Products</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['total_products'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-box text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Views</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['views_count']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-eye text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Inquiries</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['inquiries_count'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-md flex items-center justify-center">
                    <i class="fas fa-envelope text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Showroom Images -->
    @if($showroom->primary_image || ($showroom->images && count($showroom->images) > 0))
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text_md font-bold text-gray-900">Showroom Gallery</h2>
                <a href="{{ route('vendor.showrooms.gallery', $showroom->id) }}"
                   class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                    Manage Gallery <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @if($showroom->primary_image)
                        <div class="relative aspect-square rounded-lg overflow-hidden">
                            <img src="{{ $showroom->primary_image }}"
                                 alt="Primary"
                                 class="w-full h-full object-cover">
                            <div class="absolute top-2 left-2">
                                <span class="px-2 py-1 bg-[#ff0808] text-white text-xs rounded-md">Primary</span>
                            </div>
                        </div>
                    @endif
                    @if($showroom->images)
                        @foreach(array_slice($showroom->images, 0, 5) as $image)
                            <div class="relative aspect-square rounded-lg overflow-hidden">
                                <img src="{{ $image }}"
                                     alt="Gallery"
                                     class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Showroom Details -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text_md font-bold text-gray-900">Showroom Details</h2>
        </div>
        <div class="p-6 space-y-4">
            @if($showroom->description)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <p class="text-gray-900">{{ $showroom->description }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($showroom->business_type)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Business Type</label>
                        <p class="text-gray-900">{{ $showroom->business_type }}</p>
                    </div>
                @endif

                @if($showroom->industry)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                        <p class="text-gray-900">{{ $showroom->industry }}</p>
                    </div>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <p class="text-gray-900">{{ $showroom->full_address }}</p>
            </div>
        </div>
    </div>

    <!-- Products in Showroom -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text_md font-bold text-gray-900">Products ({{ $products->total() }})</h2>
            <a href="{{ route('vendor.showrooms.products', $showroom->id) }}"
               class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                Manage Products <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        @if($products->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-600">No products added yet</p>
                <a href="{{ route('vendor.showrooms.products', $showroom->id) }}"
                   class="inline-block mt-4 text-purple-600 hover:text-purple-700 font-medium">
                    Add Products <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        @else
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($products as $product)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                            <div class="aspect-square bg-gray-100">
                                @if($product->images->first())
                                    <img src="{{ $product->images->first()->image_path }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-gray-300 text-lg"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3">
                                <p class="font-medium text-gray-900 text-sm line-clamp-2 mb-1">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $product->productCategory->name ?? 'Uncategorized' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($products->hasPages())
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Sidebar -->
<div class="space-y-6">
    <!-- Quick Actions -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text_md font-bold text-gray-900">Quick Actions</h2>
        </div>
        <div class="p-4 space-y-2">
            <a href="{{ route('vendor.showrooms.edit', $showroom->id) }}"
               class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-purple-50 rounded-md transition-colors">
                <i class="fas fa-edit w-5 text-center text-purple-600"></i>
                <span class="text-sm font-medium">Edit Showroom</span>
            </a>
            <a href="{{ route('vendor.showrooms.products', $showroom->id) }}"
               class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-purple-50 rounded-md transition-colors">
                <i class="fas fa-box w-5 text-center text-purple-600"></i>
                <span class="text-sm font-medium">Manage Products</span>
            </a>
            <a href="{{ route('vendor.showrooms.gallery', $showroom->id) }}"
               class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-purple-50 rounded-md transition-colors">
                <i class="fas fa-images w-5 text-center text-purple-600"></i>
                <span class="text-sm font-medium">Manage Gallery</span>
            </a>
            <button onclick="if(confirm('Are you sure you want to delete this showroom?')) document.getElementById('delete-form').submit();"
                    class="w-full flex items-center gap-3 px-4 py-3 text-red-700 hover:bg-red-50 rounded-md transition-colors">
                <i class="fas fa-trash w-5 text-center"></i>
                <span class="text-sm font-medium">Delete Showroom</span>
            </button>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text_md font-bold text-gray-900">Contact Information</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                <p class="text-gray-900">{{ $showroom->contact_person }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <a href="mailto:{{ $showroom->email }}" class="text-purple-600 hover:text-purple-700">{{ $showroom->email }}</a>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <a href="tel:{{ $showroom->phone }}" class="text-purple-600 hover:text-purple-700">{{ $showroom->phone }}</a>
            </div>
            @if($showroom->whatsapp)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                    <a href="https://wa.me/{{ $showroom->whatsapp }}" target="_blank" class="text-purple-600 hover:text-purple-700">{{ $showroom->whatsapp }}</a>
                </div>
            @endif
            @if($showroom->website_url)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <a href="{{ $showroom->website_url }}" target="_blank" class="text-purple-600 hover:text-purple-700">Visit Website</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Status Information -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text_md font-bold text-gray-900">Status</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                @if($showroom->status === 'active')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i> Active
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-pause-circle mr-1"></i> Inactive
                    </span>
                @endif
            </div>
            @if($showroom->is_verified)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Verification</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-shield-check mr-1"></i> Verified
                    </span>
                </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Created</label>
                <p class="text-sm text-gray-900">{{ $showroom->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Delete Form -->
<form id="delete-form" action="{{ route('vendor.showrooms.destroy', $showroom->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection
