@extends('layouts.home')

@push('styles')
<style>
    .package-card { transition: transform 0.3s, box-shadow 0.3s; }
    .package-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .feature-check { animation: scaleIn 0.3s ease-in-out; }
    @keyframes scaleIn {
        from { transform: scale(0); }
        to { transform: scale(1); }
    }
</style>
@endpush

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-3">Choose Your Agent Package</h1>
        <p class="text-lg text-gray-600">Select the perfect plan to grow your referral network</p>
    </div>

    <!-- Current Subscription Alert -->
    @if($currentSubscription)
        <div class="p-4 bg-blue-50 rounded-xl border border-blue-200 flex items-start gap-3 mb-8">
            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
            <div class="flex-1">
                <p class="text-sm font-semibold text-blue-900 mb-1">You're currently subscribed to {{ $currentSubscription->package->name }}</p>
                <p class="text-sm text-blue-700">Expires on {{ $currentSubscription->expires_at->format('M d, Y') }} ({{ $currentSubscription->daysRemaining() }} days remaining)</p>
            </div>
            <a href="{{ route('agent.packages.current') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 whitespace-nowrap">
                Manage →
            </a>
        </div>
    @endif

    <!-- Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3 mb-6">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3 mb-6">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200 flex items-start gap-3 mb-6">
            <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
            <p class="text-sm font-medium text-blue-900 flex-1">{{ session('info') }}</p>
            <button onclick="this.parentElement.remove()" class="text-blue-600 hover:text-blue-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Packages Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
        @forelse($packages as $package)
            <div class="package-card relative bg-white rounded-2xl shadow-lg border-2 {{ $package->is_featured ? 'border-purple-500' : 'border-gray-200' }} overflow-hidden">
                <!-- Featured Badge -->
                @if($package->is_featured)
                    <div class="absolute top-4 right-4 z-10">
                        <span class="px-3 py-1 bg-purple-600 text-white text-xs font-bold rounded-full">
                            MOST POPULAR
                        </span>
                    </div>
                @endif

                <!-- Package Header -->
                <div class="p-8 bg-gradient-to-br from-{{ $package->badge_color }}-50 to-{{ $package->badge_color }}-100 border-b border-{{ $package->badge_color }}-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $package->name }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ $package->description }}</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-bold text-gray-900">${{ number_format($package->price, 2) }}</span>
                        <span class="text-sm text-gray-600">/ {{ $package->billing_cycle }}</span>
                    </div>
                    @if($package->billing_cycle !== 'monthly')
                        <p class="text-xs text-gray-500 mt-1">
                            ~${{ number_format($package->getMonthlyPrice(), 2) }}/month
                        </p>
                    @endif
                </div>

                <!-- Features List -->
                <div class="p-8 space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-1 feature-check"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $package->max_referrals }} Referrals</p>
                                <p class="text-xs text-gray-500">Maximum referrals allowed</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-{{ $package->allow_rfqs ? 'check-circle text-green-500' : 'times-circle text-gray-300' }} mt-1 feature-check"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">RFQ Access</p>
                                <p class="text-xs text-gray-500">Access to request for quotes</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-{{ $package->priority_support ? 'check-circle text-green-500' : 'times-circle text-gray-300' }} mt-1 feature-check"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Priority Support</p>
                                <p class="text-xs text-gray-500">Get help faster</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-{{ $package->advanced_analytics ? 'check-circle text-green-500' : 'times-circle text-gray-300' }} mt-1 feature-check"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Advanced Analytics</p>
                                <p class="text-xs text-gray-500">Detailed reports & insights</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-1 feature-check"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $package->commission_rate }}% Commission</p>
                                <p class="text-xs text-gray-500">Earn on every referral</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-{{ $package->featured_profile ? 'check-circle text-green-500' : 'times-circle text-gray-300' }} mt-1 feature-check"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Featured Profile</p>
                                <p class="text-xs text-gray-500">Stand out in listings</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-500 mt-1 feature-check"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $package->max_payouts_per_month }} Payout{{ $package->max_payouts_per_month > 1 ? 's' : '' }}/Month</p>
                                <p class="text-xs text-gray-500">Flexible withdrawal options</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-6 space-y-3">
                        @if($currentSubscription && $currentSubscription->package_id === $package->id)
                            <button disabled class="w-full px-6 py-3 bg-gray-200 text-gray-500 rounded-xl font-semibold cursor-not-allowed">
                                Current Package
                            </button>
                        @elseif($currentSubscription)
                            <a href="{{ route('agent.packages.show', $package->id) }}" class="block w-full px-6 py-3 bg-gradient-to-r from-{{ $package->badge_color }}-500 to-{{ $package->badge_color }}-600 text-white text-center rounded-xl font-semibold hover:shadow-lg transition-all">
                                View Details
                            </a>
                        @else
                            <a href="{{ route('agent.packages.checkout', $package->id) }}" class="block w-full px-6 py-3 bg-gradient-to-r from-{{ $package->badge_color }}-500 to-{{ $package->badge_color }}-600 text-white text-center rounded-xl font-semibold hover:shadow-lg transition-all">
                                Get Started
                            </a>
                        @endif

                        <a href="{{ route('agent.packages.show', $package->id) }}" class="block w-full px-6 py-3 border-2 border-gray-300 text-gray-700 text-center rounded-xl font-semibold hover:bg-gray-50 transition-all">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box-open text-2xl text-gray-300"></i>
                </div>
                <p class="text-lg font-medium text-gray-900 mb-2">No packages available</p>
                <p class="text-sm text-gray-500">Please check back later</p>
            </div>
        @endforelse
    </div>

    <!-- Benefits Section -->
    <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Why Choose Our Agent Packages?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-line text-2xl text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Grow Your Network</h3>
                <p class="text-sm text-gray-600">Build a strong referral network and earn more commissions</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-2xl text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Priority Support</h3>
                <p class="text-sm text-gray-600">Get dedicated support to help you succeed</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-dollar-sign text-2xl text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Higher Earnings</h3>
                <p class="text-sm text-gray-600">Premium packages offer better commission rates</p>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h2>
        <div class="space-y-4">
            <div class="border-b border-gray-200 pb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Can I upgrade my package?</h3>
                <p class="text-sm text-gray-600">Yes, you can upgrade anytime. The price difference will be prorated.</p>
            </div>
            <div class="border-b border-gray-200 pb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">What happens when my subscription expires?</h3>
                <p class="text-sm text-gray-600">Your account will revert to free tier. You can renew anytime to regain access to premium features.</p>
            </div>
            <div class="pb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Can I cancel anytime?</h3>
                <p class="text-sm text-gray-600">Yes, you can cancel your subscription anytime. You'll retain access until the end of your billing period.</p>
            </div>
        </div>
    </div>
</div>
@endsection
