@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Available Addons</h1>
            <p class="mt-1 text-sm text-gray-500">
                Promote your products and services with premium placements
                @if($businessProfile->country)
                    <span class="inline-flex items-center gap-1 ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                        <i class="fas fa-globe"></i>
                        {{ $businessProfile->country->name }}
                    </span>
                @endif
            </p>
        </div>
        <a href="{{ route('vendor.addons.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
            <i class="fas fa-arrow-left"></i>
            <span>Back to My Addons</span>
        </a>
    </div>

    <!-- Info Banner -->
    <div class="p-4 bg-gradient-to-r from-pink-50 to-purple-50 border border-pink-200 rounded-lg">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-lightbulb text-pink-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 mb-1">How Addons Work</h3>
                <p class="text-sm text-gray-700 mb-2">
                    Addons place your items in premium positions across our platform, increasing visibility and engagement.
                    Choose from various high-traffic locations to maximize your reach.
                </p>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-pink-200 rounded text-xs font-medium text-pink-700">
                        <i class="fas fa-eye"></i> Increased Visibility
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-pink-200 rounded text-xs font-medium text-pink-700">
                        <i class="fas fa-chart-line"></i> Higher Engagement
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-pink-200 rounded text-xs font-medium text-pink-700">
                        <i class="fas fa-dollar-sign"></i> Boost Sales
                    </span>
                </div>
            </div>
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

    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Available Addons Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($availableAddons as $addon)
            @php
                $isOwned = in_array($addon->id, $userAddonIds);
                $locationColors = [
                    'Homepage' => ['pink-500', 'fa-home'],
                    'Products' => ['blue-500', 'fa-boxes'],
                    'Suppliers' => ['purple-500', 'fa-store'],
                    'Marketplace' => ['green-500', 'fa-shopping-bag'],
                    'Category' => ['orange-500', 'fa-list'],
                    'Search' => ['teal-500', 'fa-search'],
                ];
                $locationStyle = $locationColors[$addon->locationX] ?? ['gray-500', 'fa-map-marker-alt'];
            @endphp

            <div class="bg-white rounded-xl border-2 {{ $isOwned ? 'border-green-200 bg-green-50/30' : 'border-gray-200' }} shadow-sm hover:shadow-lg transition-all group overflow-hidden">
                <!-- Header with Gradient -->
                <div class="bg-{{ $locationStyle[0] }} p-6 text-white relative overflow-hidden">
                    @if($isOwned)
                        <div class="absolute top-2 right-2 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1"></i>
                            Active
                        </div>
                    @endif

                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-4">
                            <i class="fas {{ $locationStyle[1] }} text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-1">{{ $addon->locationX }}</h3>
                        <p class="text-sm text-white/90">{{ ucfirst(str_replace('_', ' ', $addon->locationY)) }}</p>
                    </div>

                    <!-- Decorative Elements -->
                    <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/5 rounded-full"></div>
                    <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/5 rounded-full"></div>
                </div>

                <!-- Body -->
                <div class="p-6">
                    <!-- Location Info -->
                    <div class="flex items-center gap-2 mb-4 text-sm text-gray-600">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $addon->country ? $addon->country->name : 'Available Globally' }}</span>
                    </div>

                    <!-- Features -->
                    <div class="space-y-2 mb-6">
                        <div class="flex items-start gap-2 text-sm text-gray-700">
                            <i class="fas fa-check text-green-500 mt-0.5"></i>
                            <span>Prime location visibility</span>
                        </div>
                        <div class="flex items-start gap-2 text-sm text-gray-700">
                            <i class="fas fa-check text-green-500 mt-0.5"></i>
                            <span>Increased click-through rate</span>
                        </div>
                        <div class="flex items-start gap-2 text-sm text-gray-700">
                            <i class="fas fa-check text-green-500 mt-0.5"></i>
                            <span>Flexible duration options</span>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="mb-6 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border border-gray-200">
                        <div class="flex items-end justify-between">
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Starting at</div>
                                <div class="text-2xl font-bold text-gray-900">
                                    ${{ number_format($addon->price, 0) }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-600">per</div>
                                <div class="text-sm font-semibold text-gray-900">30 days</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    @if($isOwned)
                        <button disabled class="w-full px-4 py-3 bg-gray-100 text-gray-500 rounded-lg font-medium cursor-not-allowed">
                            <i class="fas fa-check-circle mr-2"></i>
                            Already Active
                        </button>
                    @else
                        <a href="{{ route('vendor.addons.create', ['addon_id' => $addon->id]) }}"
                           class="block w-full px-4 py-3 bg-{{ $locationStyle[0] }} text-white text-center rounded-lg font-medium hover:shadow-lg transition-all group-hover:scale-105">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Purchase Addon
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3">
                <div class="flex flex-col items-center justify-center py-20">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-puzzle-piece text-4xl text-gray-300"></i>
                    </div>
                    <p class="text-lg font-semibold text-gray-900 mb-1">No addons available</p>
                    <p class="text-sm text-gray-500 mb-6">
                        There are no addons available for your country at the moment.
                    </p>
                    <a href="{{ route('vendor.addons.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-all font-medium">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to My Addons</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($availableAddons, 'hasPages') && $availableAddons->hasPages())
        <div class="flex justify-center">
            {{ $availableAddons->links() }}
        </div>
    @endif

    <!-- FAQ Section -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">
            <i class="fas fa-question-circle text-pink-600 mr-2"></i>
            Frequently Asked Questions
        </h2>

        <div class="space-y-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-2">How do addons work?</h3>
                <p class="text-sm text-gray-600">
                    Addons place your products, services, or profile in premium positions across our platform.
                    This increases visibility and engagement, leading to more clicks and conversions.
                </p>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-2">Can I purchase multiple addons?</h3>
                <p class="text-sm text-gray-600">
                    Yes! You can purchase as many addons as you need for different items and locations.
                    Each addon works independently to promote specific items.
                </p>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-2">What happens when an addon expires?</h3>
                <p class="text-sm text-gray-600">
                    When an addon expires, your item returns to its normal position. You can renew the addon
                    at any time to continue the promotion. We'll send you reminders before expiration.
                </p>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-2">Do you offer discounts for longer durations?</h3>
                <p class="text-sm text-gray-600">
                    Yes! Save 5% on 2-month plans, 10% on 3-month plans, and 15% on 6-month plans.
                    The longer you commit, the more you save.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
