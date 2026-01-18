@extends('layouts.home')

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
    }
    .document-preview { transition: transform 0.3s; }
    .document-preview:hover { transform: scale(1.02); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between no-print">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('regional.vendors.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Vendor Details</h1>
            </div>
            <div class="flex items-center gap-2">
                <p class="text-sm text-gray-500">{{ $vendor->businessProfile->business_name ?? 'N/A' }}</p>
                <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $vendor->businessProfile->country->name ?? 'N/A' }}
                </span>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            @if($vendor->businessProfile && !$vendor->businessProfile->is_admin_verified)
                <form action="{{ route('regional.vendors.verify', $vendor->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium shadow-sm">
                        <i class="fas fa-check"></i>
                        <span>Verify Business</span>
                    </button>
                </form>
            @endif
            @if($vendor->account_status === 'active')
                <form action="{{ route('regional.vendors.suspend', $vendor->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" onclick="return confirm('Are you sure you want to suspend this vendor?')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm">
                        <i class="fas fa-ban"></i>
                        <span>Suspend Account</span>
                    </button>
                </form>
            @else
                <form action="{{ route('regional.vendors.activate', $vendor->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium shadow-sm">
                        <i class="fas fa-check-circle"></i>
                        <span>Activate Account</span>
                    </button>
                </form>
            @endif
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Vendor Status Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Vendor ID</p>
                <p class="text-lg font-bold text-gray-900">#{{ $vendor->id }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Country</p>
                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $vendor->businessProfile->country->name ?? 'N/A' }}
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Registration Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ $vendor->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-gray-500">{{ $vendor->created_at->format('h:i A') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Email Status</p>
                @if($vendor->email_verified)
                    <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i> Verified
                    </span>
                @else
                    <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock mr-1"></i> Pending
                    </span>
                @endif
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Account Status</p>
                @php
                    $statusColors = [
                        'active' => ['Active', 'bg-green-100 text-green-800'],
                        'suspended' => ['Suspended', 'bg-red-100 text-red-800'],
                        'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                    ];
                    $status = $statusColors[$vendor->account_status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium {{ $status[1] }}">
                    {{ $status[0] }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Business Profile -->
            @if($vendor->businessProfile)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <!-- Cover Image -->
                    @if($vendor->businessProfile->cover_image)
                        <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-600 relative">
                            <img src="{{ asset('storage/' . $vendor->businessProfile->cover_image) }}"
                                 alt="Cover"
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-600"></div>
                    @endif

                    <!-- Logo & Business Name -->
                    <div class="px-6 pb-6">
                        <div class="flex items-end -mt-16 mb-4">
                            @if($vendor->businessProfile->logo)
                                <img src="{{ asset('storage/' . $vendor->businessProfile->logo) }}"
                                     alt="Logo"
                                     class="w-32 h-32 rounded-xl border-4 border-white shadow-lg object-cover bg-white">
                            @else
                                <div class="w-32 h-32 rounded-xl border-4 border-white shadow-lg bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-store text-4xl text-gray-400"></i>
                                </div>
                            @endif
                            <div class="ml-4 mb-2">
                                <h2 class="text-2xl font-bold text-gray-900">{{ $vendor->businessProfile->business_name }}</h2>
                                <div class="flex items-center gap-2 mt-1">
                                    @if($vendor->businessProfile->is_admin_verified)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-shield-check mr-1"></i> Afrisellers Verified
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $vendor->businessProfile->country->name ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Business Information -->
                        <div class="space-y-6 mt-6">
                            @if($vendor->businessProfile->description)
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 mb-2">About Business</h3>
                                    <p class="text-gray-700 text-sm leading-relaxed">{{ $vendor->businessProfile->description }}</p>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($vendor->businessProfile->business_type)
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <p class="text-xs font-medium text-gray-600 mb-1">Business Type</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ ucfirst($vendor->businessProfile->business_type) }}</p>
                                    </div>
                                @endif

                                @if($vendor->businessProfile->year_established)
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <p class="text-xs font-medium text-gray-600 mb-1">Year Established</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $vendor->businessProfile->year_established }}</p>
                                    </div>
                                @endif

                                @if($vendor->businessProfile->company_size)
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <p class="text-xs font-medium text-gray-600 mb-1">Company Size</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $vendor->businessProfile->company_size }}</p>
                                    </div>
                                @endif

                                @if($vendor->businessProfile->annual_revenue)
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <p class="text-xs font-medium text-gray-600 mb-1">Annual Revenue</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $vendor->businessProfile->annual_revenue }}</p>
                                    </div>
                                @endif

                                @if($vendor->businessProfile->certifications)
                                    <div class="p-3 bg-gray-50 rounded-lg md:col-span-2">
                                        <p class="text-xs font-medium text-gray-600 mb-1">Certifications</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $vendor->businessProfile->certifications }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Operations -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Operations</h3>
                    <div class="space-y-4">
                        @if($vendor->businessProfile->main_products)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Main Products</label>
                                <p class="mt-1 text-gray-900">{{ $vendor->businessProfile->main_products }}</p>
                            </div>
                        @endif

                        @if($vendor->businessProfile->export_markets)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Export Markets</label>
                                <p class="mt-1 text-gray-900">{{ $vendor->businessProfile->export_markets }}</p>
                            </div>
                        @endif

                        @if($vendor->businessProfile->production_capacity)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Production Capacity</label>
                                <p class="mt-1 text-gray-900">{{ $vendor->businessProfile->production_capacity }}</p>
                            </div>
                        @endif

                        @if($vendor->businessProfile->quality_control)
                            <div>
                                <label class="text-sm font-medium text-gray-700">Quality Control</label>
                                <p class="mt-1 text-gray-900">{{ $vendor->businessProfile->quality_control }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($vendor->businessProfile->payment_terms)
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Payment Terms</label>
                                    <p class="mt-1 text-gray-900">{{ $vendor->businessProfile->payment_terms }}</p>
                                </div>
                            @endif

                            @if($vendor->businessProfile->delivery_time)
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Delivery Time</label>
                                    <p class="mt-1 text-gray-900">{{ $vendor->businessProfile->delivery_time }}</p>
                                </div>
                            @endif

                            @if($vendor->businessProfile->minimum_order_value)
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Minimum Order Value</label>
                                    <p class="mt-1 text-gray-900">${{ number_format($vendor->businessProfile->minimum_order_value, 2) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Registration Documents -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Registration Documents</h3>
                    <div class="space-y-3">
                        @if($vendor->businessProfile->business_registration_number)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-600 mb-1">Business Registration Number</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $vendor->businessProfile->business_registration_number }}</p>
                            </div>
                        @endif

                        @if($vendor->ownerID)
                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-id-card text-blue-600"></i>
                                    <p class="text-sm font-semibold text-blue-900">Owner ID Document</p>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-blue-700">Type:</span>
                                        <span class="font-medium text-blue-900 ml-1">{{ $vendor->ownerID->id_type }}</span>
                                    </div>
                                    <div>
                                        <span class="text-blue-700">Number:</span>
                                        <span class="font-medium text-blue-900 ml-1">{{ $vendor->ownerID->id_number }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                <div class="space-y-4">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs font-medium text-blue-600 mb-1">Total Products</p>
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($stats['total_products']) }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <p class="text-xs font-medium text-green-600 mb-1">Active Products</p>
                        <p class="text-2xl font-bold text-green-900">{{ number_format($stats['active_products']) }}</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <p class="text-xs font-medium text-purple-600 mb-1">Verified Products</p>
                        <p class="text-2xl font-bold text-purple-900">{{ number_format($stats['verified_products']) }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <p class="text-xs font-medium text-indigo-600 mb-1">Total Views</p>
                        <p class="text-2xl font-bold text-indigo-900">{{ number_format($stats['total_views']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Owner Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Owner Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full">
                            <span class="text-lg font-bold text-blue-700">{{ substr($vendor->user->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $vendor->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Account Owner</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-envelope text-gray-400 w-5"></i>
                            <span class="text-gray-900">{{ $vendor->user->email ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            @if($vendor->businessProfile)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                    <div class="space-y-3 text-sm">
                        @if($vendor->businessProfile->business_email)
                            <div class="flex items-start gap-2">
                                <i class="fas fa-envelope text-gray-400 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Business Email</p>
                                    <p class="text-gray-900 font-medium">{{ $vendor->businessProfile->business_email }}</p>
                                </div>
                            </div>
                        @endif

                        @if($vendor->businessProfile->phone)
                            <div class="flex items-start gap-2">
                                <i class="fas fa-phone text-gray-400 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Phone</p>
                                    <p class="text-gray-900 font-medium">{{ $vendor->businessProfile->full_phone }}</p>
                                </div>
                            </div>
                        @endif

                        @if($vendor->businessProfile->whatsapp_number)
                            <div class="flex items-start gap-2">
                                <i class="fab fa-whatsapp text-gray-400 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">WhatsApp</p>
                                    <p class="text-gray-900 font-medium">{{ $vendor->businessProfile->whatsapp_number }}</p>
                                </div>
                            </div>
                        @endif

                        @if($vendor->businessProfile->address)
                            <div class="flex items-start gap-2">
                                <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Address</p>
                                    <p class="text-gray-900">{{ $vendor->businessProfile->address }}</p>
                                    <p class="text-gray-700">{{ $vendor->businessProfile->city }}, {{ $vendor->businessProfile->postal_code }}</p>
                                    <p class="text-gray-700 font-medium">{{ $vendor->businessProfile->country->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif

                        @if($vendor->businessProfile->website)
                            <div class="flex items-start gap-2">
                                <i class="fas fa-globe text-gray-400 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Website</p>
                                    <a href="{{ $vendor->businessProfile->website }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $vendor->businessProfile->website }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Social Media -->
                @if($vendor->businessProfile->hasSocialMedia())
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Social Media</h3>
                        <div class="flex flex-wrap gap-3">
                            @if($vendor->businessProfile->facebook_link)
                                <a href="{{ $vendor->businessProfile->facebook_link }}" target="_blank" class="p-3 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100">
                                    <i class="fab fa-facebook text-xl"></i>
                                </a>
                            @endif
                            @if($vendor->businessProfile->twitter_link)
                                <a href="{{ $vendor->businessProfile->twitter_link }}" target="_blank" class="p-3 bg-sky-50 text-sky-600 rounded-lg hover:bg-sky-100">
                                    <i class="fab fa-twitter text-xl"></i>
                                </a>
                            @endif
                            @if($vendor->businessProfile->linkedin_link)
                                <a href="{{ $vendor->businessProfile->linkedin_link }}" target="_blank" class="p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100">
                                    <i class="fab fa-linkedin text-xl"></i>
                                </a>
                            @endif
                            @if($vendor->businessProfile->instagram_link)
                                <a href="{{ $vendor->businessProfile->instagram_link }}" target="_blank" class="p-3 bg-pink-50 text-pink-600 rounded-lg hover:bg-pink-100">
                                    <i class="fab fa-instagram text-xl"></i>
                                </a>
                            @endif
                            @if($vendor->businessProfile->youtube_link)
                                <a href="{{ $vendor->businessProfile->youtube_link }}" target="_blank" class="p-3 bg-red-50 text-red-600 rounded-lg hover:bg-red-100">
                                    <i class="fab fa-youtube text-xl"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Contact Person -->
                @if($vendor->businessProfile->contact_person_name)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Person</h3>
                        <div class="space-y-2 text-sm">
                            <div>
                                <p class="text-xs text-gray-500">Name</p>
                                <p class="text-gray-900 font-semibold">{{ $vendor->businessProfile->contact_person_name }}</p>
                            </div>
                            @if($vendor->businessProfile->contact_person_position)
                                <div>
                                    <p class="text-xs text-gray-500">Position</p>
                                    <p class="text-gray-900">{{ $vendor->businessProfile->contact_person_position }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 no-print">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    @if($vendor->businessProfile && !$vendor->businessProfile->is_admin_verified)
                        <form action="{{ route('regional.vendors.verify', $vendor->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm">
                                <i class="fas fa-check mr-2"></i> Verify Business
                            </button>
                        </form>
                    @endif
                    @if($vendor->account_status === 'active')
                        <form action="{{ route('regional.vendors.suspend', $vendor->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Are you sure you want to suspend this vendor?')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-sm">
                                <i class="fas fa-ban mr-2"></i> Suspend Account
                            </button>
                        </form>
                    @else
                        <form action="{{ route('regional.vendors.activate', $vendor->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm">
                                <i class="fas fa-check-circle mr-2"></i> Activate Account
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
