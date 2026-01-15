{{-- resources/views/frontend/tradeshows/show.blade.php --}}

@extends('layouts.app')

@section('title', $tradeshow->name . ' - AfriSellers')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">

        <!-- Breadcrumb -->
        <nav class="mb-6 text-xs text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-[#ff0808]">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('tradeshows.index') }}" class="hover:text-[#ff0808]">Trade Shows</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">{{ $tradeshow->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-4">

                <!-- Banner/Image -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="relative h-80 bg-gradient-to-br from-{{ ['red', 'blue', 'purple'][$tradeshow->id % 3] }}-100 to-{{ ['red', 'blue', 'purple'][$tradeshow->id % 3] }}-200">
                        @if($tradeshow->banner_image)
                            <img src="{{ $tradeshow->banner_image }}" alt="{{ $tradeshow->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full">
                                <div class="text-center">
                                    <span class="text-8xl">🎪</span>
                                    <h2 class="text-lg font-bold text-gray-900 mt-4">{{ $tradeshow->name }}</h2>
                                </div>
                            </div>
                        @endif

                        @if($tradeshow->is_featured)
                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-amber-500 text-white text-xs font-semibold rounded-lg shadow-lg">
                                ⭐ FEATURED
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Event Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $tradeshow->name }}</h1>
                    <p class="text-sm text-gray-600 mb-4">{{ $tradeshow->industry }} • {{ $tradeshow->category }}</p>

                    <!-- Key Stats -->
                    <div class="grid grid-cols-3 gap-3 mb-5 pb-5 border-b border-gray-100">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div class="text-xl font-bold text-blue-600">{{ number_format($tradeshow->expected_visitors) }}+</div>
                            <div class="text-xs text-gray-600">Expected Visitors</div>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-xl font-bold text-green-600">{{ $tradeshow->expected_exhibitors }}+</div>
                            <div class="text-xs text-gray-600">Exhibitors</div>
                        </div>
                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                            <div class="text-xl font-bold text-orange-600">{{ $tradeshow->duration_days }}</div>
                            <div class="text-xs text-gray-600">Days</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-5">
                        <h3 class="text-base font-semibold text-gray-900 mb-2">About This Event</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $tradeshow->description }}</p>
                    </div>

                    <!-- Event Details -->
                    <div class="mb-5 pb-5 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900 mb-3">Event Details</h3>

                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-calendar-alt text-[#ff0808] text-sm mt-0.5"></i>
                                <div class="flex-1">
                                    <div class="text-xs text-gray-600">Date</div>
                                    <div class="font-medium text-gray-900 text-sm">
                                        {{ $tradeshow->start_date->format('l, F d, Y') }} - {{ $tradeshow->end_date->format('l, F d, Y') }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <i class="fas fa-clock text-[#ff0808] text-sm mt-0.5"></i>
                                <div class="flex-1">
                                    <div class="text-xs text-gray-600">Time</div>
                                    <div class="font-medium text-gray-900 text-sm">
                                        {{ $tradeshow->start_time ? $tradeshow->start_time : '09:00' }} - {{ $tradeshow->end_time ? $tradeshow->end_time : '18:00' }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <i class="fas fa-map-marker-alt text-[#ff0808] text-sm mt-0.5"></i>
                                <div class="flex-1">
                                    <div class="text-xs text-gray-600">Venue</div>
                                    <div class="font-medium text-gray-900 text-sm">{{ $tradeshow->venue_name }}</div>
                                    <div class="text-xs text-gray-600">{{ $tradeshow->venue_address }}, {{ $tradeshow->city }}, {{ $tradeshow->country->name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    @if($tradeshow->features && count($tradeshow->features) > 0)
                        <div class="mb-5 pb-5 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900 mb-3">Event Features</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($tradeshow->features as $feature)
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg border border-blue-200">
                                        <i class="fas fa-check-circle mr-1"></i>{{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Target Audience -->
                    @if($tradeshow->target_audience && count($tradeshow->target_audience) > 0)
                        <div class="mb-5">
                            <h3 class="text-base font-semibold text-gray-900 mb-2">Who Should Attend</h3>
                            <ul class="space-y-1">
                                @foreach($tradeshow->target_audience as $audience)
                                    <li class="text-sm text-gray-700 flex items-center gap-2">
                                        <i class="fas fa-user-check text-[#ff0808] text-xs"></i>
                                        {{ $audience }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif>

                    <!-- Contact Information -->
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold text-gray-900 text-sm mb-2">Contact Information</h4>
                        <div class="space-y-1 text-xs">
                            @if($tradeshow->contact_email)
                                <p><i class="fas fa-envelope text-[#ff0808] mr-2"></i>{{ $tradeshow->contact_email }}</p>
                            @endif
                            @if($tradeshow->contact_phone)
                                <p><i class="fas fa-phone text-[#ff0808] mr-2"></i>{{ $tradeshow->contact_phone }}</p>
                            @endif
                            @if($tradeshow->website_url)
                                <p><i class="fas fa-globe text-[#ff0808] mr-2"></i>
                                    <a href="{{ $tradeshow->website_url }}" target="_blank" class="text-blue-600 hover:underline">Visit Website</a>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-4">

                    <!-- Pricing -->
                    <div class="mb-5 pb-5 border-b border-gray-100">
                        @if($tradeshow->free_entry)
                            <div class="text-2xl font-bold text-green-600 mb-1">Free Entry</div>
                            <p class="text-xs text-gray-600">No registration fee required</p>
                        @elseif($tradeshow->visitor_ticket_price)
                            <div class="text-lg font-bold text-gray-900 mb-1">
                                ${{ number_format($tradeshow->visitor_ticket_price, 0) }}
                            </div>
                            <p class="text-xs text-gray-600">Visitor Ticket Price</p>
                        @endif

                        @if($tradeshow->booth_price_from && $tradeshow->booth_price_to)
                            <div class="mt-3 p-2 bg-blue-50 rounded-lg">
                                <p class="text-xs text-blue-700 font-medium">Exhibitor Booth</p>
                                <p class="text-sm font-semibold text-blue-900">
                                    ${{ number_format($tradeshow->booth_price_from, 0) }} - ${{ number_format($tradeshow->booth_price_to, 0) }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Registration -->
                    @if($tradeshow->registration_required)
                        <div class="mb-5 pb-5 border-b border-gray-100">
                            <h4 class="font-semibold text-gray-900 text-sm mb-2">Registration</h4>
                            @if($tradeshow->registration_deadline)
                                <p class="text-xs text-gray-600 mb-2">
                                    Deadline: {{ $tradeshow->registration_deadline->format('M d, Y') }}
                                </p>
                            @endif
                            @if($tradeshow->registration_url)
                                <a href="{{ $tradeshow->registration_url }}" target="_blank"
                                   class="block w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors text-center">
                                    <i class="fas fa-external-link-alt mr-1.5"></i>Register Now
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    @auth
                        <button onclick="alert('Registration feature coming soon!')"
                                class="w-full px-4 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors mb-2">
                            <i class="fas fa-ticket-alt mr-1.5"></i>Get Tickets
                        </button>
                        <button onclick="alert('Inquiry feature coming soon!')"
                                class="w-full px-4 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                            <i class="fas fa-envelope mr-1.5"></i>Send Inquiry
                        </button>
                    @else
                        <a href="{{ route('auth.signin') }}"
                           class="block w-full px-4 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors text-center">
                            <i class="fas fa-sign-in-alt mr-1.5"></i>Sign In to Register
                        </a>
                    @endauth

                    <!-- Organizer Info -->
                    @if($tradeshow->organizer_name)
                        <div class="mt-5 pt-5 border-t border-gray-100">
                            <h4 class="font-semibold text-gray-900 text-sm mb-2">Organized By</h4>
                            <p class="text-sm font-medium text-gray-900">{{ $tradeshow->organizer_name }}</p>
                            @if($tradeshow->organizer_website)
                                <a href="{{ $tradeshow->organizer_website }}" target="_blank"
                                   class="text-xs text-blue-600 hover:underline">Visit Website</a>
                            @endif
                        </div>
                    @endif

                    <!-- Stats -->
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <div class="text-center">
                            <div class="text-xl font-bold text-gray-900">{{ number_format($tradeshow->views_count) }}</div>
                            <div class="text-xs text-gray-600">Views</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Tradeshows -->
        @if($similarTradeshows->count() > 0)
            <div class="mt-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Similar Events</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($similarTradeshows as $similar)
                        <a href="{{ route('tradeshows.show', $similar->slug) }}"
                           class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all overflow-hidden">
                            <div class="h-20 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white">
                                <div class="text-center">
                                    <div class="text-xl font-black">{{ strtoupper($similar->start_date->format('M')) }}</div>
                                    <div class="text-lg font-black">{{ $similar->start_date->format('d') }}</div>
                                </div>
                            </div>
                            <div class="p-3">
                                <h3 class="font-semibold text-gray-900 text-sm mb-1 line-clamp-2">{{ $similar->name }}</h3>
                                <p class="text-xs text-gray-600 mb-1">{{ $similar->city }}, {{ $similar->country->name }}</p>
                                <p class="text-xs text-gray-500">{{ $similar->start_date->format('M d, Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
