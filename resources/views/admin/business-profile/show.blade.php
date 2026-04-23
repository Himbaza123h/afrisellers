@extends('layouts.home')

@section('page-content')

{{-- ════════════════════════════════════════════════════════════
     HEADER
═══════════════════════════════════════════════════════════════ --}}
<div class="mb-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-black text-gray-900 uppercase sm:text-2xl lg:text-lg">Business Profile Details</h1>
            <p class="mt-1 text-xs text-gray-600 sm:text-sm">Review business profile, documents and products</p>
        </div>
        <a href="{{ route('admin.business-profile.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-semibold text-sm">
            <i class="fas fa-arrow-left"></i>
            Back to List
        </a>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════
     ALERTS
═══════════════════════════════════════════════════════════════ --}}
@if(session('success'))
    <div class="p-4 mb-4 bg-green-50 rounded-lg border border-green-300">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="p-4 mb-4 bg-red-50 rounded-lg border border-red-300">
        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
    </div>
@endif

@if($errors->any())
    <div class="p-4 mb-4 bg-red-50 rounded-lg border border-red-300">
        <p class="mb-2 text-sm font-medium text-red-900">Please fix the following errors:</p>
        <ul class="space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ════════════════════════════════════════════════════════════
     MAIN GRID
═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- ── LEFT / MAIN COLUMN ───────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Business Information --------------------------------}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Business Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Business Name</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $businessProfile->business_name }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Registration Number</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->business_registration_number ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Phone Number</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $businessProfile->phone_code }} {{ $businessProfile->phone }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Country</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->country->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">City</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->city ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Submitted Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Owner Information ----------------------------------}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Owner Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Owner Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->user->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->user->email ?? 'N/A' }}</p>
                    </div>
                    @if($ownerID)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">ID / Passport Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $ownerID->id_number ?? 'N/A' }}</p>
                        </div>
                    @endif
                    @if($businessProfile->user)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Member Since</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->user->created_at->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Submitted Documents --------------------------------}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Submitted Documents</h2>
            </div>
            <div class="p-6">
                @php
                    $hasBusinessDoc = ($ownerID && $ownerID->business_document_path) || $businessProfile->business_registration_doc;
                    $hasIdDoc       = $ownerID && $ownerID->id_document_path;
                    $hasExtraDoc    = $businessProfile->extra_document;
                @endphp

                @if($hasBusinessDoc || $hasIdDoc || $hasExtraDoc)
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        {{-- Business Registration Document --}}
                        @if($ownerID && $ownerID->business_document_path)
                            <div class="flex flex-col gap-3 p-4 rounded-xl bg-gray-50 border border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-pdf text-[#ff0808]"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Business Registration</p>
                                        <p class="text-xs text-gray-500">Official registration document</p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($ownerID->business_document_path) }}" target="_blank"
                                   class="inline-flex justify-center gap-2 items-center px-4 py-2 text-sm font-semibold text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                    View Document
                                </a>
                            </div>
                        @elseif($businessProfile->business_registration_doc)
                            <div class="flex flex-col gap-3 p-4 rounded-xl bg-gray-50 border border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-pdf text-[#ff0808]"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Business Registration</p>
                                        <p class="text-xs text-gray-500">Official registration document</p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($businessProfile->business_registration_doc) }}" target="_blank"
                                   class="inline-flex justify-center gap-2 items-center px-4 py-2 text-sm font-semibold text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                    View Document
                                </a>
                            </div>
                        @endif

                        {{-- Owner ID Document --}}
                        @if($hasIdDoc)
                            <div class="flex flex-col gap-3 p-4 rounded-xl bg-gray-50 border border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-id-card text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Owner ID / Passport</p>
                                        <p class="text-xs text-gray-500">Identity verification document</p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($ownerID->id_document_path) }}" target="_blank"
                                   class="inline-flex justify-center gap-2 items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                    View Document
                                </a>
                            </div>
                        @endif
                        {{-- Extra / Resubmitted Document --}}
@if($hasExtraDoc)
    <div class="flex flex-col gap-3 p-4 rounded-xl bg-gray-50 border border-gray-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-file-alt text-purple-600"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Extra Document</p>
                <p class="text-xs text-gray-500">
                    {{ $businessProfile->extra_document_original_name ?? 'Resubmitted document' }}
                    @if($businessProfile->extra_document_uploaded_at)
                        &mdash; {{ \Carbon\Carbon::parse($businessProfile->extra_document_uploaded_at)->format('M d, Y') }}
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ Storage::url($businessProfile->extra_document) }}" target="_blank"
           class="inline-flex justify-center gap-2 items-center px-4 py-2 text-sm font-semibold text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-colors">
            <i class="fas fa-external-link-alt text-xs"></i>
            View Document
        </a>
    </div>
@endif
                    </div>
                @else
                    <div class="py-10 flex flex-col items-center justify-center text-center text-gray-400">
                        <i class="fas fa-folder-open text-4xl mb-3"></i>
                        <p class="text-sm font-medium">No documents submitted</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────
             PRODUCTS SECTION
        ────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-bold text-gray-900">Products</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                        {{ $productStats['total'] }}
                    </span>
                </div>
                <a href="{{ route('admin.vendor.product.create') }}?user_id={{ $businessProfile->user_id }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            </div>

            {{-- Product mini-stats --}}
            <div class="grid grid-cols-3 divide-x divide-gray-100 border-b border-gray-100">
                <div class="px-6 py-3 text-center">
                    <p class="text-lg font-black text-green-700">{{ $productStats['approved'] }}</p>
                    <p class="text-xs text-gray-500">Approved</p>
                </div>
                <div class="px-6 py-3 text-center">
                    <p class="text-lg font-black text-yellow-600">{{ $productStats['pending'] }}</p>
                    <p class="text-xs text-gray-500">Pending</p>
                </div>
                <div class="px-6 py-3 text-center">
                    <p class="text-lg font-black text-red-600">{{ $productStats['rejected'] }}</p>
                    <p class="text-xs text-gray-500">Rejected</p>
                </div>
            </div>

            {{-- Product Filters --}}
            <div class="px-6 py-3 border-b border-gray-100 bg-gray-50">
                <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap gap-2 items-center">
                    <input type="text" name="prod_search" value="{{ request('prod_search') }}"
                           placeholder="Search products..."
                           class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#ff0808] focus:border-[#ff0808] w-44">

                    <select name="prod_status" class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#ff0808]">
                        <option value="">All Status</option>
                        <option value="pending"  {{ request('prod_status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('prod_status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('prod_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>

                    <select name="prod_category" class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#ff0808]">
                        <option value="">All Categories</option>
                        @foreach($productCategories as $cat)
                            <option value="{{ $cat->id }}" {{ request('prod_category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="px-3 py-1.5 text-xs font-semibold text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                    <a href="{{ url()->current() }}"
                       class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Reset
                    </a>
                </form>
            </div>

            {{-- Products Grid --}}
            @if($vendorProducts->isNotEmpty())
                <div class="p-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-3">
                    @foreach($vendorProducts as $product)
                        @php
                            $primaryImg  = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                            $statusColor = match($product->status) {
                                'active'   => 'bg-green-500',
                                'inactive' => 'bg-red-500',
                                default    => 'bg-yellow-500',  // draft
                            };
                        @endphp
                        <div class="group bg-white rounded-xl border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all overflow-hidden">
                            {{-- Image --}}
                            <div class="aspect-video bg-gray-100 overflow-hidden relative">
                                @if($primaryImg)
                                <img src="{{ str_starts_with($primaryImg->image_url, 'http') ? $primaryImg->image_url : Storage::url($primaryImg->image_url) }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-box text-gray-300 text-3xl"></i>
                                    </div>
                                @endif
                                <span class="absolute top-2 left-2 px-2 py-0.5 text-[10px] font-bold text-white rounded-full {{ $statusColor }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                                @if($product->images->count() > 1)
                                    <span class="absolute bottom-2 right-2 px-1.5 py-0.5 text-[10px] font-semibold text-white bg-black/50 rounded-full">
                                        <i class="fas fa-images mr-0.5"></i>{{ $product->images->count() }}
                                    </span>
                                @endif
                                @if($product->is_admin_verified)
                                    <span class="absolute top-2 right-2 w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center" title="Admin Verified">
                                        <i class="fas fa-check text-white" style="font-size: 8px;"></i>
                                    </span>
                                @endif
                            </div>
                            {{-- Info --}}
                            <div class="p-3">
                                <p class="text-sm font-semibold text-gray-900 line-clamp-1">{{ $product->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $product->productCategory->name ?? '—' }}</p>
                                {{-- Actions --}}
                                <div class="flex gap-1.5 mt-3">
                                    <a href="{{ route('admin.vendor.product.show', $product) }}"
                                       class="flex-1 inline-flex justify-center items-center py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    <a href="{{ route('admin.vendor.product.edit', $product) }}"
                                       class="flex-1 inline-flex justify-center items-center py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-pencil mr-1"></i> Edit
                                    </a>
                                </div>
                                @if($product->status === 'pending')
                                    <div class="flex gap-1.5 mt-1.5">
                                        <form action="{{ route('admin.vendor.product.approve', $product) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full py-1.5 text-xs font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                                <i class="fas fa-check mr-1"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.vendor.product.reject', $product) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full py-1.5 text-xs font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                                <i class="fas fa-times mr-1"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Products Pagination --}}
                @if($vendorProducts->hasPages())
                    <div class="px-6 py-3 border-t border-gray-100">
                        {{ $vendorProducts->appends(request()->except('prod_page'))->links() }}
                    </div>
                @endif
            @else
                <div class="py-14 flex flex-col items-center justify-center text-center">
                    <i class="fas fa-box text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500 font-medium">No products found</p>
                    <p class="text-gray-400 text-xs mt-1">
                        @if(request('prod_search') || request('prod_status') || request('prod_category'))
                            Try adjusting your filters
                        @else
                            This vendor has not added any products yet
                        @endif
                    </p>
                </div>
            @endif
        </div>
        {{-- END PRODUCTS SECTION --}}

    </div>
    {{-- END LEFT COLUMN --}}

    {{-- ── RIGHT / SIDEBAR ─────────────────────────────────── --}}
    <div class="space-y-6">

        {{-- Status Card --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Status</h2>
            </div>
            <div class="p-6 space-y-4">
                @php
                    $statusBadge = match($businessProfile->verification_status) {
                        'verified'  => ['bg-green-100 text-green-800',  'fa-check-circle',  'Verified'],
                        'rejected'  => ['bg-red-100 text-red-800',      'fa-times-circle',  'Rejected'],
                        'suspended' => ['bg-orange-100 text-orange-800','fa-ban',            'Suspended'],
                        default     => ['bg-yellow-100 text-yellow-800','fa-clock',          'Pending Review'],
                    };
                @endphp
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusBadge[0] }}">
                        <i class="fas {{ $statusBadge[1] }}"></i>
                        {{ $statusBadge[2] }}
                    </span>
                </div>

                <div class="space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between items-center py-1 border-b border-gray-100">
                        <span class="text-gray-500">Verification Status</span>
                        <span class="font-semibold">{{ ucfirst($businessProfile->verification_status) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-gray-100">
                        <span class="text-gray-500">Admin Verified</span>
                        <span class="font-semibold {{ $businessProfile->is_admin_verified ? 'text-green-700' : 'text-gray-400' }}">
                            {{ $businessProfile->is_admin_verified ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    @if($vendor)
                        <div class="flex justify-between items-center py-1 border-b border-gray-100">
                            <span class="text-gray-500">Vendor Account</span>
                            <span class="font-semibold text-green-700">Created</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-gray-500">Vendor Status</span>
                            <span class="font-semibold">{{ ucfirst($vendor->status ?? '—') }}</span>
                        </div>
                    @else
                        <div class="flex justify-between items-center py-1">
                            <span class="text-gray-500">Vendor Account</span>
                            <span class="font-semibold text-gray-400">Not created</span>
                        </div>
                    @endif
                </div>

        @if($businessProfile->rejection_reason)
            <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                <p class="text-xs font-semibold text-red-700 mb-1">Rejection Reason:</p>
                <p class="text-xs text-red-600">{{ $businessProfile->rejection_reason }}</p>
            </div>
        @endif

        @if($businessProfile->verification_status === 'rejected' && $businessProfile->reason_reply)
            <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-xs font-semibold text-blue-700 mb-1">
                    <i class="fas fa-reply mr-1"></i> Vendor's Reply:
                </p>
                <p class="text-xs text-gray-700">{{ $businessProfile->reason_reply }}</p>
            </div>
        @elseif($businessProfile->verification_status === 'rejected')
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs text-gray-400 italic">
                    <i class="fas fa-clock mr-1"></i> Vendor has not replied yet.
                </p>
            </div>
        @endif
            </div>
        </div>

        {{-- Actions Card --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Actions</h2>
            </div>
            <div class="p-6 space-y-3">

                {{-- Verify & Create Vendor --}}
                @if($businessProfile->verification_status !== 'verified')
                    <form action="{{ route('admin.business-profile.verify', $businessProfile) }}" method="POST"
                          onsubmit="return confirm('Verify this business profile and create a vendor account?')">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check-circle"></i>
                            Verify &amp; Create Vendor
                        </button>
                    </form>
                @endif

                {{-- Activate --}}
                @if(in_array($businessProfile->verification_status, ['suspended', 'rejected']))
                    <form action="{{ route('admin.business-profile.activate', $businessProfile) }}" method="POST"
                          onsubmit="return confirm('Reactivate this business profile?')">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-power-off"></i>
                            Activate Profile
                        </button>
                    </form>
                @endif

                {{-- Suspend --}}
                @if($businessProfile->verification_status === 'verified')
                    <form action="{{ route('admin.business-profile.suspend', $businessProfile) }}" method="POST"
                          onsubmit="return confirm('Suspend this business profile?')">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors">
                            <i class="fas fa-ban"></i>
                            Suspend Profile
                        </button>
                    </form>
                @endif

                {{-- Reject --}}
                {{-- @if($businessProfile->verification_status !== 'rejected')
                    <div x-data="{ open: false }">
                        <button type="button"
                                onclick="document.getElementById('rejectPanel').classList.toggle('hidden')"
                                class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-times-circle"></i>
                            Reject Application
                        </button>
                        <div id="rejectPanel" class="hidden mt-3">
                            <form action="{{ route('admin.business-profile.reject', $businessProfile) }}" method="POST"
                                  onsubmit="return confirm('Reject this business profile?')">
                                @csrf
                                <div class="mb-3">
                                    <label class="block mb-1 text-xs font-medium text-gray-700">
                                        Rejection Reason <span class="text-gray-400">(optional)</span>
                                    </label>
                                    <textarea name="rejection_reason" rows="3"
                                              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-red-400"
                                              placeholder="Enter reason for rejection...">{{ old('rejection_reason') }}</textarea>
                                </div>
                                <button type="submit"
                                        class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                    Confirm Rejection
                                </button>
                            </form>
                        </div>
                    </div>
                @endif --}}

                {{-- Reject --}}
        @if($businessProfile->verification_status !== 'rejected')
            <div>
                <button type="button"
                        onclick="document.getElementById('rejectPanel').classList.toggle('hidden')"
                        class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-times-circle"></i>
                    Reject Application
                </button>
                <div id="rejectPanel" class="hidden mt-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-xs font-semibold text-red-700 mb-3">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Enter a reason for rejection
                    </p>
                    <form action="{{ route('admin.business-profile.reject', $businessProfile) }}" method="POST"
                        id="showRejectForm">
                        @csrf
                        <div class="mb-3">
                            <textarea name="rejection_reason" rows="3" required
                                    class="w-full px-3 py-2 text-sm border border-red-300 rounded-lg focus:ring-2 focus:ring-red-400 focus:border-red-400 bg-white"
                                    placeholder="Enter reason for rejection...">{{ old('rejection_reason') }}</textarea>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" id="showRejectSubmitBtn"
                                    class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-ban" id="showRejectIcon"></i>
                                <span id="showRejectText">Confirm Rejection</span>
                            </button>
                            <button type="button"
                                    onclick="document.getElementById('rejectPanel').classList.add('hidden')"
                                    class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

            <script>
            document.getElementById('showRejectForm')?.addEventListener('submit', function () {
                const btn  = document.getElementById('showRejectSubmitBtn');
                const icon = document.getElementById('showRejectIcon');
                const text = document.getElementById('showRejectText');
                btn.disabled = true;
                icon.className = 'fas fa-spinner fa-spin';
                text.textContent = 'Rejecting...';
            });
            </script>

                {{-- View Vendor Profile (if exists) --}}
                @if($vendor)
                    <div class="pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.vendors.show', $vendor) }}"
                           class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-store"></i>
                            View Vendor Profile
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Product Stats --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-base font-bold text-gray-900">Product Summary</h2>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Products</span>
                    <span class="text-sm font-bold text-gray-900">{{ $productStats['total'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Approved</span>
                    <span class="text-sm font-bold text-green-700">{{ $productStats['approved'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pending Review</span>
                    <span class="text-sm font-bold text-yellow-600">{{ $productStats['pending'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Rejected</span>
                    <span class="text-sm font-bold text-red-600">{{ $productStats['rejected'] }}</span>
                </div>

                @if($productStats['total'] > 0)
                    <div class="pt-3">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Approval rate</span>
                            <span>{{ round(($productStats['approved'] / $productStats['total']) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full"
                                 style="width: {{ round(($productStats['approved'] / $productStats['total']) * 100) }}%">
                            </div>
                        </div>
                    </div>
                @endif

                <div class="pt-2">
                    <a href="{{ route('admin.vendor.product.index') }}?vendor={{ $businessProfile->user_id }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#ff0808] hover:underline">
                        <i class="fas fa-external-link-alt"></i> View All Products
                    </a>
                </div>
            </div>
        </div>


        {{-- Danger Zone --}}
<div class="bg-white rounded-xl border border-red-200 shadow-sm">
    <div class="px-6 py-4 border-b border-red-200 bg-red-50 rounded-t-xl">
        <h2 class="text-base font-bold text-red-700 flex items-center gap-2">
            <i class="fas fa-skull-crossbones"></i> Danger Zone
        </h2>
    </div>
    <div class="p-6">
        <p class="text-xs text-gray-500 mb-4">
            Permanently deletes this user, their business profile, and vendor account (if any). This action uses soft delete but cannot be undone from the UI.
        </p>

        <button type="button"
                onclick="document.getElementById('deleteUserPanel').classList.toggle('hidden')"
                class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-red-700 rounded-lg hover:bg-red-800 transition-colors">
            <i class="fas fa-trash-alt"></i>
            Delete This User
        </button>

        <div id="deleteUserPanel" class="hidden mt-4 p-4 bg-red-50 border border-red-300 rounded-lg">
            <p class="text-xs font-semibold text-red-700 mb-1">
                <i class="fas fa-exclamation-triangle mr-1"></i> Type the following to confirm:
            </p>
            <code class="block text-xs bg-white border border-red-200 rounded px-2 py-1 mb-3 text-red-800 font-mono select-all">
                delete-{{ $businessProfile->user->email ?? '' }}
            </code>

            <form action="{{ route('admin.business-profile.destroy-user', $businessProfile) }}" method="POST" id="deleteUserForm">
                @csrf
                @method('DELETE')
                <input type="text"
                       name="confirmation"
                       id="deleteConfirmInput"
                       placeholder="Type confirmation here..."
                       autocomplete="off"
                       class="w-full px-3 py-2 text-sm border border-red-300 rounded-lg mb-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">

                <div class="flex gap-2">
                    <button type="submit"
                            id="deleteUserSubmitBtn"
                            disabled
                            class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-red-700 rounded-lg hover:bg-red-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                        <i class="fas fa-trash-alt" id="deleteUserIcon"></i>
                        <span id="deleteUserText">Delete Permanently</span>
                    </button>
                    <button type="button"
                            onclick="document.getElementById('deleteUserPanel').classList.add('hidden')"
                            class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const input    = document.getElementById('deleteConfirmInput');
    const btn      = document.getElementById('deleteUserSubmitBtn');
    const expected = 'delete-{{ $businessProfile->user->email ?? '' }}';

    input?.addEventListener('input', function () {
        btn.disabled = this.value.trim() !== expected;
    });

    document.getElementById('deleteUserForm')?.addEventListener('submit', function () {
        btn.disabled = true;
        document.getElementById('deleteUserIcon').className = 'fas fa-spinner fa-spin';
        document.getElementById('deleteUserText').textContent = 'Deleting...';
    });
})();
</script>

    </div>
    {{-- END SIDEBAR --}}

</div>
@endsection
