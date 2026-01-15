{{-- resources/views/frontend/tradeshows/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Trade Shows - AfriSellers')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">

        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="mb-1 text-xl font-black text-gray-900 sm:text-2xl lg:text-lg sm:mb-2">
                        Trade Shows & Exhibitions
                    </h1>
                    <p class="text-xs text-gray-600 sm:text-sm">Discover upcoming business events across Africa</p>
                </div>
                <a href="{{ route('showrooms.index') }}"
                   class="flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-calendar-alt text-[#ff0808]"></i>
                    <span>View Showrooms</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            <!-- Filters Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-4">
                    <div class="flex items-center gap-2 mb-5">
                        <i class="fas fa-sliders-h text-[#ff0808]"></i>
                        <h3 class="text-base font-semibold text-gray-900">Filters</h3>
                    </div>

                    <form method="GET" action="{{ route('tradeshows.index') }}" class="space-y-4">

                        <!-- Country -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Country</label>
                            <select name="country_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Countries</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Industry -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Industry</label>
                            <select name="industry" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Industries</option>
                                @foreach($industries as $industry)
                                    <option value="{{ $industry }}" {{ request('industry') == $industry ? 'selected' : '' }}>
                                        {{ $industry }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Type</label>
                            <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Types</option>
                                <option value="B2B" {{ request('category') == 'B2B' ? 'selected' : '' }}>B2B</option>
                                <option value="B2C" {{ request('category') == 'B2C' ? 'selected' : '' }}>B2C</option>
                                <option value="Mixed" {{ request('category') == 'Mixed' ? 'selected' : '' }}>Mixed</option>
                            </select>
                        </div>

                        <!-- Month -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Month</label>
                            <select name="month" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-[#ff0808] focus:ring-1 focus:ring-[#ff0808] focus:outline-none text-sm">
                                <option value="">All Months</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Show Past Events -->
                        <div class="flex items-center">
                            <input type="checkbox" name="show_all" id="show_all" value="1"
                                   {{ request('show_all') ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#ff0808] border-gray-300 rounded focus:ring-[#ff0808]">
                            <label for="show_all" class="ml-2 text-xs text-gray-700">Show past events</label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                                Apply
                            </button>
                            <a href="{{ route('tradeshows.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tradeshows Grid -->
            <div class="lg:col-span-3">

                <!-- Results Count -->
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs text-gray-500">
                        Showing {{ $tradeshows->firstItem() ?? 0 }} - {{ $tradeshows->lastItem() ?? 0 }} of {{ $tradeshows->total() }} tradeshows
                    </p>
                </div>

                @if($tradeshows->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($tradeshows as $tradeshow)
                            <a href="{{ route('tradeshows.show', $tradeshow->slug) }}"
                               class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-300 transition-all overflow-hidden group">

                                <!-- Date Banner -->
                                <div class="relative h-24 bg-gradient-to-br from-{{ ['red', 'blue', 'purple', 'green', 'orange'][$loop->index % 5] }}-400 to-{{ ['red', 'blue', 'purple', 'green', 'orange'][$loop->index % 5] }}-600 flex items-center justify-center text-white">
                                    <div class="text-center">
                                        <div class="text-2xl font-black">{{ strtoupper($tradeshow->start_date->format('M')) }}</div>
                                        <div class="text-4xl font-black">{{ $tradeshow->start_date->format('d') }}</div>
                                    </div>

                                    @if($tradeshow->is_featured)
                                        <span class="absolute top-2 right-2 px-2 py-0.5 bg-amber-500 text-white text-xs font-semibold rounded shadow-sm">
                                            FEATURED
                                        </span>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="p-4">
                                    <h3 class="text-base font-semibold text-gray-900 mb-1 group-hover:text-[#ff0808] transition-colors line-clamp-2">
                                        {{ $tradeshow->name }}
                                    </h3>

                                    <p class="text-xs text-gray-600 mb-3">{{ $tradeshow->industry }}</p>

                                    <!-- Location -->
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fas fa-map-marker-alt text-[#ff0808] text-xs"></i>
                                        <span class="text-xs text-gray-700 font-medium">{{ $tradeshow->city }}, {{ $tradeshow->country->name }}</span>
                                    </div>

                                    <!-- Venue -->
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fas fa-building text-[#ff0808] text-xs"></i>
                                        <span class="text-xs text-gray-600 truncate">{{ $tradeshow->venue_name }}</span>
                                    </div>

                                    <!-- Duration -->
                                    <div class="flex items-center gap-2 mb-3">
                                        <i class="fas fa-calendar text-[#ff0808] text-xs"></i>
                                        <span class="text-xs text-gray-600">
                                            {{ $tradeshow->start_date->format('M d') }} - {{ $tradeshow->end_date->format('M d, Y') }}
                                        </span>
                                    </div>

                                    <!-- Stats -->
                                    <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                                        <span class="text-xs text-gray-600">
                                            <i class="fas fa-users text-[#ff0808]"></i>
                                            {{ number_format($tradeshow->expected_visitors) }}+ visitors
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            <i class="fas fa-store text-[#ff0808]"></i>
                                            {{ $tradeshow->expected_exhibitors }}+ exhibitors
                                        </span>
                                    </div>

                                    <!-- Price Tag -->
                                    @if($tradeshow->free_entry)
                                        <div class="mt-3 px-2 py-1 bg-green-50 text-green-700 text-xs font-medium rounded inline-block">
                                            Free Entry
                                        </div>
                                    @elseif($tradeshow->visitor_ticket_price)
                                        <div class="mt-3 text-sm font-semibold text-gray-900">
                                            From ${{ number_format($tradeshow->visitor_ticket_price, 0) }}
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $tradeshows->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <div class="text-5xl mb-3">📅</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">No tradeshows found</h3>
                        <p class="text-sm text-gray-500 mb-4">Try adjusting your filters to see more results</p>
                        <a href="{{ route('tradeshows.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                            <i class="fas fa-redo-alt"></i>
                            Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
