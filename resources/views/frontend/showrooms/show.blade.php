

@extends('layouts.app')

@section('title', $showroom->name . ' - AfriSellers')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">

        <!-- Breadcrumb -->
        <nav class="mb-6 text-xs text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-[#ff0808]">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('showrooms.index') }}" class="hover:text-[#ff0808]">Showrooms</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">{{ $showroom->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-4">

                <!-- Image Gallery -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="relative h-80 bg-gray-100">
                        @if($showroom->primary_image)
                            <img src="{{ $showroom->primary_image }}" alt="{{ $showroom->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full">
                                <span class="text-8xl">🏢</span>
                            </div>
                        @endif

                        @if($showroom->is_featured)
                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-amber-500 text-white text-xs font-semibold rounded-lg shadow-lg">
                                ⭐ FEATURED
                            </span>
                        @endif

                        @if($showroom->is_verified)
                            <span class="absolute top-3 right-3 px-2.5 py-1 bg-green-500 text-white text-xs font-semibold rounded-lg shadow-lg">
                                <i class="fas fa-check-circle"></i> Verified
                            </span>
                        @endif
                    </div>

                    <!-- Thumbnail Gallery (if images exist) -->
                    @if($showroom->images && count($showroom->images) > 1)
                        <div class="p-3 flex gap-2 overflow-x-auto">
                            @foreach($showroom->images as $image)
                                <img src="{{ $image }}" alt="Showroom image"
                                     class="w-16 h-16 object-cover rounded-lg cursor-pointer hover:ring-2 hover:ring-[#ff0808] flex-shrink-0">
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Showroom Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $showroom->name }}</h1>
                    <div class="flex items-center gap-2 mb-4">
                        <p class="text-sm text-gray-600">{{ $showroom->business_type }}</p>
                        <span class="text-gray-300">•</span>
                        <p class="text-sm text-gray-600">{{ $showroom->industry }}</p>
                    </div>

                        <!-- Quick Stats Bar -->

                        <!-- Quick Stats Bar -->
                <div class="flex items-center gap-4 mb-5 pb-5 border-b border-gray-100">
                    <a href="{{ route('showrooms.products', $showroom->slug) }}"
                    class="flex items-center gap-3 px-6 py-2 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                        <i class="fas fa-box text-blue-600"></i>
                        <div>
                            <div class="text-xs text-gray-600">Products</div>
                            <div class="text-sm font-bold text-blue-600">{{ $showroom->products()->count() }}</div>
                        </div>
                    </a>
                    <div class="flex items-center gap-3 px-6 py-2 bg-gray-50 rounded-lg">
                        <i class="fas fa-eye text-gray-600"></i>
                        <div>
                            <div class="text-xs text-gray-600">Views</div>
                            <div class="text-sm font-bold text-gray-900">{{ number_format($showroom->views_count) }}</div>
                        </div>
                    </div>
                </div>



                    <!-- Rating -->
                    @if($showroom->rating > 0)
                        <div class="flex items-center gap-3 mb-5 pb-5 border-b border-gray-100">
                            <div class="flex items-center gap-1.5 text-amber-500">
                                <i class="fas fa-star"></i>
                                <span class="font-semibold text-gray-900 text-base">{{ number_format($showroom->rating, 1) }}</span>
                            </div>
                            <span class="text-sm text-gray-600">{{ $showroom->reviews_count }} reviews</span>
                            @if($showroom->years_in_business)
                                <span class="text-gray-300">|</span>
                                <span class="text-sm text-gray-600">{{ $showroom->years_in_business }} years in business</span>
                            @endif
                        </div>
                    @endif

                    <!-- Description -->
                    <div class="mb-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-2">About</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $showroom->description }}</p>
                    </div>

                        <!-- View Products Button -->
                    <a href="{{ route('showrooms.products', $showroom->slug) }}"
                    class="block w-full px-4 py-3 bg-blue-600 text-white text-center rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg mb-5">
                        <i class="fas fa-shopping-bag mr-2"></i>View All Products
                    </a>

                    <!-- Location & Hours -->
                    <div class="mb-5 pb-5 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900 mb-3">Location & Hours</h3>

                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-map-marker-alt text-[#ff0808] text-sm mt-0.5"></i>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900 text-sm">{{ $showroom->address }}</div>
                                    <div class="text-xs text-gray-600">{{ $showroom->city }}, {{ $showroom->country->name }}</div>
                                </div>
                            </div>

                            @if($showroom->operating_hours)
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-clock text-[#ff0808] text-sm mt-0.5"></i>
                                    <div class="flex-1">
                                        <div class="text-xs text-gray-600 mb-1">Operating Hours</div>
                                        <div class="grid grid-cols-3 gap-1">
                                            @foreach($showroom->operating_hours as $day => $hours)
                                                <div class="flex justify-between text-xs">
                                                    <span class="font-medium text-gray-700 capitalize">{{ $day }}:</span>
                                                    <span class="text-gray-600">{{ $hours }}</span>
                                                </div>
                                            @endforeach
                                            </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Services -->
                    @if($showroom->services && count($showroom->services) > 0)
                        <div class="mb-5 pb-5 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900 mb-3">Services</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($showroom->services as $service)
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg border border-blue-200">
                                        <i class="fas fa-check-circle mr-1"></i>{{ $service }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Brands Carried -->
                    @if($showroom->brands_carried && count($showroom->brands_carried) > 0)
                        <div class="mb-5 pb-5 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900 mb-3">Brands We Carry</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($showroom->brands_carried as $brand)
                                    <span class="px-2.5 py-1 bg-gray-50 text-gray-700 text-xs font-medium rounded-lg border border-gray-200">
                                        {{ $brand }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Facilities -->
                    @if($showroom->facilities && count($showroom->facilities) > 0)
                        <div class="mb-5">
                            <h3 class="text-base font-semibold text-gray-900 mb-3">Facilities</h3>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($showroom->facilities as $facility)
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <i class="fas fa-check text-green-600 text-xs"></i>
                                        {{ $facility }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-4">

                    <!-- Contact Info -->
                    <div class="mb-5 pb-5 border-b border-gray-100">
                        <h4 class="font-semibold text-gray-900 text-sm mb-3">Contact Information</h4>
                        <div class="space-y-2 text-sm">
                            @if($showroom->phone)
                                <a href="tel:{{ $showroom->phone }}" class="flex items-center gap-2 text-gray-700 hover:text-[#ff0808]">
                                    <i class="fas fa-phone text-[#ff0808]"></i>
                                    {{ $showroom->phone }}
                                </a>
                            @endif
                            @if($showroom->email)
                                <a href="mailto:{{ $showroom->email }}" class="flex items-center gap-2 text-gray-700 hover:text-[#ff0808]">
                                    <i class="fas fa-envelope text-[#ff0808]"></i>
                                    {{ $showroom->email }}
                                </a>
                            @endif
                            @if($showroom->website_url)
                                <a href="{{ $showroom->website_url }}" target="_blank" class="flex items-center gap-2 text-blue-600 hover:underline">
                                    <i class="fas fa-globe text-[#ff0808]"></i>
                                    Visit Website
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @auth
                        <button onclick="alert('Inquiry feature coming soon!')"
                                class="w-full px-4 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors mb-2">
                            <i class="fas fa-envelope mr-1.5"></i>Send Inquiry
                        </button>
                        <button onclick="alert('Visit scheduling coming soon!')"
                                class="w-full px-4 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                            <i class="fas fa-calendar-check mr-1.5"></i>Schedule Visit
                        </button>
                    @else
                        <a href="{{ route('auth.signin') }}"
                           class="block w-full px-4 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors text-center mb-2">
                            <i class="fas fa-sign-in-alt mr-1.5"></i>Sign In to Inquire
                        </a>
                    @endauth

                    <!-- Special Features -->
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <div class="space-y-2 text-xs">
                            @if($showroom->walk_ins_welcome)
                                <div class="flex items-center gap-2 text-green-700">
                                    <i class="fas fa-door-open"></i>
                                    Walk-ins Welcome
                                </div>
                            @endif
                            @if($showroom->appointment_required)
                                <div class="flex items-center gap-2 text-blue-700">
                                    <i class="fas fa-calendar-alt"></i>
                                    Appointment Required
                                </div>
                            @endif
                            @if($showroom->has_parking)
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas fa-parking"></i>
                                    {{ $showroom->parking_spaces ? $showroom->parking_spaces . ' parking spaces' : 'Parking Available' }}
                                </div>
                            @endif
                            @if($showroom->wheelchair_accessible)
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas fa-wheelchair"></i>
                                    Wheelchair Accessible
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Owner/Contact Person -->
                    @if($showroom->contact_person)
                        <div class="mt-5 pt-5 border-t border-gray-100">
                            <h4 class="font-semibold text-gray-900 text-sm mb-2">Contact Person</h4>
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 bg-[#ff0808] rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ strtoupper(substr($showroom->contact_person, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 text-sm">{{ $showroom->contact_person }}</div>
                                    @if($showroom->years_in_business)
                                        <div class="text-xs text-gray-600">{{ $showroom->years_in_business }} years experience</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Stats -->
                    <div class="mt-5 pt-5 border-t border-gray-100 grid grid-cols-2 gap-3 text-center">
                        <div>
                            <div class="text-xl font-bold text-gray-900">{{ number_format($showroom->views_count) }}</div>
                            <div class="text-xs text-gray-600">Views</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-gray-900">{{ number_format($showroom->inquiries_count) }}</div>
                            <div class="text-xs text-gray-600">Inquiries</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Showrooms -->
        @if($similarShowrooms->count() > 0)
            <div class="mt-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Similar Showrooms</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($similarShowrooms as $similar)
                        <a href="{{ route('showrooms.show', $similar->slug) }}"
                           class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all">
                            <div class="h-36 bg-gray-100 rounded-t-xl overflow-hidden">
                                @if($similar->primary_image)
                                    <img src="{{ $similar->primary_image }}" class="w-full h-full object-cover">
                                @else
                                    <div class="flex items-center justify-center h-full text-4xl">🏢</div>
                                @endif
                            </div>
                            <div class="p-3">
                                <h3 class="font-semibold text-gray-900 text-sm mb-1 line-clamp-1">{{ $similar->name }}</h3>
                                <p class="text-xs text-gray-600 mb-1">{{ $similar->industry }}</p>
                                <p class="text-xs text-gray-500">{{ $similar->city }}</p>
                                @if($similar->rating > 0)
                                    <p class="mt-1 text-xs text-amber-600">⭐ {{ number_format($similar->rating, 1) }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
