@extends('layouts.app')

@section('title', $load->cargo_type . ' - LoadBoard')

@section('content')
<div class="bg-gray-50 min-h-screen py-6 md:py-8">
    <div class="container mx-auto px-4">

        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-[#ff0808]">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('loadboard.loads.index') }}" class="hover:text-[#ff0808]">Loads</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-semibold">{{ $load->load_number }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Load Header -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center text-4xl flex-shrink-0">
                            📦
                        </div>
                        <div class="flex-1">
                            <h1 class="text-2xl md:text-lg font-black text-gray-900 mb-2">{{ $load->cargo_type }}</h1>
                            <p class="text-gray-600 mb-3">{{ $load->cargo_description }}</p>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full">
                                    {{ ucfirst($load->status) }}
                                </span>
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">
                                    {{ $bidCount }} {{ $bidCount === 1 ? 'Bid' : 'Bids' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Route Information -->
                    <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-red-50 rounded-lg">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Route</h3>
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                                    <span class="text-sm text-gray-600">Pickup Location</span>
                                </div>
                                <div class="font-bold text-gray-900">{{ $load->origin_city }}</div>
                                <div class="text-sm text-gray-600">{{ $load->originCountry->name }}</div>
                                @if($load->origin_address)
                                    <div class="text-xs text-gray-500 mt-1">{{ $load->origin_address }}</div>
                                @endif
                            </div>

                            <div class="flex flex-col items-center">
                                <i class="fas fa-arrow-right text-2xl text-[#ff0808]"></i>
                                <span class="text-xs text-gray-600 mt-1">Transport</span>
                            </div>

                            <div class="flex-1 text-right">
                                <div class="flex items-center gap-2 justify-end mb-2">
                                    <span class="text-sm text-gray-600">Delivery Location</span>
                                    <i class="fas fa-map-marker-alt text-red-600 text-xl"></i>
                                </div>
                                <div class="font-bold text-gray-900">{{ $load->destination_city }}</div>
                                <div class="text-sm text-gray-600">{{ $load->destinationCountry->name }}</div>
                                @if($load->destination_address)
                                    <div class="text-xs text-gray-500 mt-1">{{ $load->destination_address }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Load Details -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-600 mb-1">Weight</div>
                            <div class="font-bold text-gray-900">{{ number_format($load->weight) }} {{ $load->weight_unit }}</div>
                        </div>
                        @if($load->volume)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-600 mb-1">Volume</div>
                                <div class="font-bold text-gray-900">{{ number_format($load->volume) }} {{ $load->volume_unit }}</div>
                            </div>
                        @endif
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-600 mb-1">Quantity</div>
                            <div class="font-bold text-gray-900">{{ $load->quantity ?? 'N/A' }}</div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="text-xs text-gray-600 mb-1">Packaging</div>
                            <div class="font-bold text-gray-900">{{ $load->packaging_type ?? 'Standard' }}</div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-bold text-gray-900 mb-3">Timeline</h4>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-calendar-check text-green-600"></i>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Pickup Date</div>
                                    <div class="text-sm text-gray-600">
                                        {{ $load->pickup_date->format('l, M d, Y') }}
                                        @if($load->pickup_time_start)
                                            - {{ $load->pickup_time_start->format('H:i') }}
                                            @if($load->pickup_time_end)
                                                to {{ $load->pickup_time_end->format('H:i') }}
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($load->delivery_date)
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-calendar-alt text-red-600"></i>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">Delivery Date</div>
                                        <div class="text-sm text-gray-600">
                                            {{ $load->delivery_date->format('l, M d, Y') }}
                                            @if($load->delivery_time_start)
                                                - {{ $load->delivery_time_start->format('H:i') }}
                                                @if($load->delivery_time_end)
                                                    to {{ $load->delivery_time_end->format('H:i') }}
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Special Requirements -->
                    @if($load->special_requirements && count($load->special_requirements) > 0)
                        <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                Special Requirements
                            </h4>
                            <ul class="space-y-2">
                                @foreach($load->special_requirements as $requirement)
                                    <li class="flex items-start gap-2 text-sm text-gray-700">
                                        <i class="fas fa-check-circle text-yellow-600 mt-0.5"></i>
                                        <span>{{ $requirement }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Additional Notes -->
                    @if($load->notes)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-bold text-gray-900 mb-2">Additional Notes</h4>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $load->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Similar Loads -->
                @if($similarLoads->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Similar Loads</h3>
                        <div class="space-y-3">
                            @foreach($similarLoads as $similar)
                                <a href="{{ route('loadboard.loads.show', $similar->load_number) }}"
                                   class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex justify-between items-start gap-3">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900 mb-1">{{ $similar->cargo_type }}</h4>
                                            <p class="text-sm text-gray-600">
                                                {{ $similar->origin_city }} → {{ $similar->destination_city }}
                                            </p>
                                        </div>
                                        @if($similar->budget)
                                            <div class="text-lg font-bold text-[#ff0808]">
                                                ${{ number_format($similar->budget, 0) }}
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4 space-y-6">

                    <!-- Budget -->
                    <div class="pb-6 border-b border-gray-200">
                        @if($load->budget)
                            <div class="text-sm text-gray-600 mb-1">Budget</div>
                            <div class="text-4xl font-black text-[#ff0808] mb-2">
                                ${{ number_format($load->budget, 0) }}
                            </div>
                            <div class="text-sm text-gray-600">{{ $load->currency }}</div>
                        @else
                            <div class="text-2xl font-bold text-gray-900">Budget: Negotiable</div>
                            <p class="text-sm text-gray-600 mt-2">Submit your best offer</p>
                        @endif

                        @if($load->pricing_type)
                            <div class="mt-3 px-3 py-1.5 bg-blue-50 text-blue-700 text-sm font-semibold rounded-lg inline-block">
                                {{ ucfirst(str_replace('_', ' ', $load->pricing_type)) }}
                            </div>
                        @endif
                    </div>

                    <!-- Bid Button -->
                    @auth
                        <button onclick="openBidModal()" class="w-full px-4 py-3 bg-[#ff0808] text-white rounded-lg font-bold hover:bg-red-700 transition-colors">
                            <i class="fas fa-gavel mr-2"></i>Place Bid
                        </button>
                    @else
                        <a href="{{ route('auth.signin') }}" class="block w-full px-4 py-3 bg-[#ff0808] text-white rounded-lg font-bold hover:bg-red-700 transition-colors text-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In to Bid
                        </a>
                    @endauth

                    <!-- Load Owner -->
                    <div class="pt-6 border-t border-gray-200">
                        <h4 class="font-bold text-gray-900 mb-3">Posted By</h4>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-[#ff0808] rounded-full flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($load->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $load->user->name }}</div>
                                <div class="text-sm text-gray-600">Member since {{ $load->user->created_at->format('Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-[#ff0808]">{{ $bidCount }}</div>
                                <div class="text-xs text-gray-600">Bids Received</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-[#ff0808]">
                                    {{ $load->created_at->diffForHumans(null, true) }}
                                </div>
                                <div class="text-xs text-gray-600">Posted</div>
                            </div>
                        </div>
                    </div>

                    <!-- Share -->
                    <div class="pt-6 border-t border-gray-200">
                        <h4 class="font-bold text-gray-900 mb-3">Share this load</h4>
                        <div class="flex gap-2">
                            <button class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button class="flex-1 px-3 py-2 bg-sky-500 text-white rounded-lg text-sm font-semibold hover:bg-sky-600">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="flex-1 px-3 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button class="flex-1 px-3 py-2 bg-gray-600 text-white rounded-lg text-sm font-semibold hover:bg-gray-700">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bid Modal (Add your modal markup here) -->
@endsection
