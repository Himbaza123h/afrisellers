@extends('layouts.home')

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
    }
    .stat-card { transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between no-print">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('country.showrooms.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Showroom Details</h1>
            </div>
            <div class="flex items-center gap-2">
                <p class="text-sm text-gray-500">{{ $showroom->name }}</p>
                <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $showroom->country->name ?? 'N/A' }}
                </span>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            @if(!$showroom->is_verified)
                <form action="{{ route('country.showrooms.verify', $showroom->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium shadow-sm">
                        <i class="fas fa-check"></i>
                        <span>Verify Showroom</span>
                    </button>
                </form>
            @endif
            <form action="{{ route('country.showrooms.feature', $showroom->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 {{ $showroom->is_featured ? 'bg-yellow-600' : 'bg-gray-600' }} text-white rounded-lg hover:bg-yellow-700 transition-all font-medium shadow-sm">
                    <i class="fas fa-star"></i>
                    <span>{{ $showroom->is_featured ? 'Unfeature' : 'Feature' }}</span>
                </button>
            </form>
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

    <!-- Status Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Showroom ID</p>
                <p class="text-lg font-bold text-gray-900">{{ $showroom->showroom_number }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Location</p>
                <p class="text-sm font-semibold text-gray-900">{{ $showroom->city }}</p>
                <p class="text-xs text-gray-500">{{ $showroom->country->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Created Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ $showroom->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-gray-500">{{ $showroom->created_at->format('h:i A') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Verification</p>
                @if($showroom->is_verified)
                    <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i> Verified
                    </span>
                @else
                    <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times-circle mr-1"></i> Unverified
                    </span>
                @endif
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Status</p>
                @php
                    $statusColors = [
                        'active' => ['Active', 'bg-green-100 text-green-800'],
                        'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                        'inactive' => ['Inactive', 'bg-gray-100 text-gray-800'],
                    ];
                    $status = $statusColors[$showroom->status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
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
            <!-- Showroom Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                @if($showroom->primary_image)
                    <div class="h-64 bg-gradient-to-r from-blue-500 to-purple-600 relative">
                        <img src="{{ asset('storage/' . $showroom->primary_image) }}"
                             alt="Showroom"
                             class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="h-64 bg-gradient-to-r from-blue-500 to-purple-600"></div>
                @endif

                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $showroom->name }}</h2>
                            <div class="flex items-center gap-2 mt-2">
                                @if($showroom->is_verified)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-shield-check mr-1"></i> Verified
                                    </span>
                                @endif
                                @if($showroom->is_featured)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-star mr-1"></i> Featured
                                    </span>
                                @endif
                                @if($showroom->is_authorized_dealer)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-certificate mr-1"></i> Authorized Dealer
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($showroom->description)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">About</h3>
                            <p class="text-gray-700 text-sm leading-relaxed">{{ $showroom->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($showroom->business_type)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-600 mb-1">Business Type</p>
                                <p class="text-sm font-semibold text-gray-900">{{ ucfirst($showroom->business_type) }}</p>
                            </div>
                        @endif

                        @if($showroom->industry)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-600 mb-1">Industry</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $showroom->industry }}</p>
                            </div>
                        @endif

                        @if($showroom->showroom_size_sqm)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-600 mb-1">Showroom Size</p>
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($showroom->showroom_size_sqm) }} sqm</p>
                            </div>
                        @endif

                        @if($showroom->established_date)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-600 mb-1">Established</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $showroom->established_date->format('Y') }}</p>
                            </div>
                        @endif

                        @if($showroom->employees_count)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-600 mb-1">Employees</p>
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($showroom->employees_count) }}</p>
                            </div>
                        @endif

                        @if($showroom->parking_spaces)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-600 mb-1">Parking Spaces</p>
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($showroom->parking_spaces) }}</p>
                            </div>
                        @endif
                    </div>

                    @if($showroom->brands_carried && count($showroom->brands_carried) > 0)
                        <div class="mt-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Brands Carried</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($showroom->brands_carried as $brand)
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">{{ $brand }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($showroom->services && count($showroom->services) > 0)
                        <div class="mt-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Services Offered</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($showroom->services as $service)
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                        <i class="fas fa-check mr-1"></i>{{ $service }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($showroom->facilities && count($showroom->facilities) > 0)
                        <div class="mt-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Facilities</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($showroom->facilities as $facility)
                                    <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-medium">
                                        <i class="fas fa-check mr-1"></i>{{ $facility }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($showroom->languages_spoken && count($showroom->languages_spoken) > 0)
                        <div class="mt-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Languages Spoken</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($showroom->languages_spoken as $language)
                                    <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-medium">
                                        <i class="fas fa-language mr-1"></i>{{ $language }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Operating Hours -->
            @if($showroom->operating_hours)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Operating Hours</h3>
                    <div class="space-y-2">
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            @if(isset($showroom->operating_hours[$day]))
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-900 capitalize">{{ $day }}</span>
                                    <span class="text-sm text-gray-600">{{ $showroom->operating_hours[$day] }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Products -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Products ({{ $showroom->products->count() }})</h3>
                @if($showroom->products->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($showroom->products->take(6) as $product)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                @if($product->images->first())
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                         alt="{{ $product->name }}"
                                         class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ Str::limit($product->name, 30) }}</p>
                                    @if($product->prices->first())
                                        <p class="text-sm text-gray-600">${{ number_format($product->prices->first()->price, 2) }}</p>
                                    @endif
                                    <span class="text-xs {{ $product->status === 'active' ? 'text-green-600' : 'text-gray-500' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($showroom->products->count() > 6)
                        <p class="text-sm text-gray-500 mt-4 text-center">And {{ $showroom->products->count() - 6 }} more products...</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500">No products listed yet.</p>
                @endif
            </div>
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
                    <div class="p-3 bg-teal-50 rounded-lg">
                        <p class="text-xs font-medium text-teal-600 mb-1">Total Inquiries</p>
                        <p class="text-2xl font-bold text-teal-900">{{ number_format($stats['total_inquiries']) }}</p>
                    </div>
                    <div class="p-3 bg-orange-50 rounded-lg">
                        <p class="text-xs font-medium text-orange-600 mb-1">Total Visits</p>
                        <p class="text-2xl font-bold text-orange-900">{{ number_format($stats['total_visits']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Owner Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Owner Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full">
                            <span class="text-lg font-bold text-blue-700">{{ substr($showroom->user->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $showroom->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Showroom Owner</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-envelope text-gray-400 w-5"></i>
                            <span class="text-gray-900">{{ $showroom->user->email ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                <div class="space-y-3 text-sm">
                    @if($showroom->email)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-envelope text-gray-400 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-gray-900 font-medium">{{ $showroom->email }}</p>
                            </div>
                        </div>
                    @endif

                    @if($showroom->phone)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-phone text-gray-400 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Phone</p>
                                <p class="text-gray-900 font-medium">{{ $showroom->phone }}</p>
                            </div>
                        </div>
                    @endif

                    @if($showroom->alternate_phone)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-phone-alt text-gray-400 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Alternate Phone</p>
                                <p class="text-gray-900 font-medium">{{ $showroom->alternate_phone }}</p>
                            </div>
                        </div>
                    @endif

                    @if($showroom->whatsapp)
                        <div class="flex items-start gap-2">
                            <i class="fab fa-whatsapp text-gray-400 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">WhatsApp</p>
                                <p class="text-gray-900 font-medium">{{ $showroom->whatsapp }}</p>
                            </div>
                        </div>
                    @endif

                    @if($showroom->address)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Address</p>
                                <p class="text-gray-900">{{ $showroom->address }}</p>
                                <p class="text-gray-700">{{ $showroom->city }}@if($showroom->state_province), {{ $showroom->state_province }}@endif</p>
                                @if($showroom->postal_code)
                                    <p class="text-gray-700">{{ $showroom->postal_code }}</p>
                                @endif
                                <p class="text-gray-700 font-medium">{{ $showroom->country->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @endif

                    @if($showroom->website_url)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-globe text-gray-400 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Website</p>
                                <a href="{{ $showroom->website_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $showroom->website_url }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Social Media -->
            @if($showroom->facebook_url || $showroom->instagram_url || $showroom->twitter_url || $showroom->linkedin_url)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Social Media</h3>
                    <div class="flex flex-wrap gap-3">
                        @if($showroom->facebook_url)
                            <a href="{{ $showroom->facebook_url }}" target="_blank" class="p-3 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100">
                                <i class="fab fa-facebook text-xl"></i>
                            </a>
                        @endif
                        @if($showroom->instagram_url)
                            <a href="{{ $showroom->instagram_url }}" target="_blank" class="p-3 bg-pink-50 text-pink-600 rounded-lg hover:bg-pink-100">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                        @endif
                        @if($showroom->twitter_url)
                            <a href="{{ $showroom->twitter_url }}" target="_blank" class="p-3 bg-sky-50 text-sky-600 rounded-lg hover:bg-sky-100">
                                <i class="fab fa-twitter text-xl"></i>
                            </a>
                        @endif
                        @if($showroom->linkedin_url)
                            <a href="{{ $showroom->linkedin_url }}" target="_blank" class="p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100">
                                <i class="fab fa-linkedin text-xl"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Contact Person -->
            @if($showroom->contact_person)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Person</h3>
                    <div class="space-y-2 text-sm">
                        <div>
                            <p class="text-xs text-gray-500">Name</p>
                            <p class="text-gray-900 font-semibold">{{ $showroom->contact_person }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 no-print">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    @if(!$showroom->is_verified)
                        <form action="{{ route('country.showrooms.verify', $showroom->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm">
                                <i class="fas fa-check mr-2"></i> Verify Showroom
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('country.showrooms.feature', $showroom->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 {{ $showroom->is_featured ? 'bg-yellow-600' : 'bg-gray-600' }} text-white rounded-lg hover:bg-yellow-700 font-medium text-sm">
                            <i class="fas fa-star mr-2"></i> {{ $showroom->is_featured ? 'Unfeature' : 'Feature' }}
                        </button>
                    </form>
                    <form action="{{ route('country.showrooms.destroy', $showroom->id) }}" method="POST" onsubmit="return confirm('Delete this showroom?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-sm">
                            <i class="fas fa-trash mr-2"></i> Delete Showroom
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
