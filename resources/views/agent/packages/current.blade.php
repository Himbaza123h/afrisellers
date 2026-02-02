@extends('layouts.home')

@section('page-content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Subscription</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your agent package subscription</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.open('{{ route('agent.packages.print') }}', '_blank')" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('agent.packages.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium shadow-sm">
                <i class="fas fa-box"></i>
                <span>View Packages</span>
            </a>
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

    @if($subscription)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Subscription Status -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-6 bg-gradient-to-br from-{{ $subscription->package->badge_color }}-50 to-{{ $subscription->package->badge_color }}-100 border-b border-{{ $subscription->package->badge_color }}-200">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $subscription->package->name }}</h2>
                                <p class="text-sm text-gray-600">{{ $subscription->package->description }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $subscription->status_badge }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Subscription Info -->
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Started On</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $subscription->starts_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Expires On</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $subscription->expires_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Days Remaining</p>
                                <p class="text-lg font-bold text-{{ $subscription->daysRemaining() < 7 ? 'red' : 'gray' }}-900">
                                    {{ $subscription->daysRemaining() }} days
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Amount Paid</p>
                                <p class="text-lg font-bold text-gray-900">${{ number_format($subscription->amount_paid, 2) }}</p>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-medium text-gray-700">Subscription Progress</p>
                                <p class="text-xs font-semibold text-gray-900">{{ $stats['progress_percentage'] }}%</p>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all" style="width: {{ $stats['progress_percentage'] }}%"></div>
                            </div>
                        </div>

                        <!-- Auto-Renewal -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-sync-alt text-gray-400"></i>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Auto-Renewal</p>
                                    <p class="text-xs text-gray-500">{{ $subscription->auto_renew ? 'Enabled' : 'Disabled' }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $subscription->auto_renew ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $subscription->auto_renew ? 'ON' : 'OFF' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Usage Statistics -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Usage Statistics</h3>

                    <div class="space-y-6">
                        <!-- Referrals Usage -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-users text-blue-600"></i>
                                    <span class="text-sm font-semibold text-gray-900">Referrals</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">
                                    {{ $subscription->referrals_used }} / {{ $subscription->package->max_referrals }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $subscription->getReferralUsagePercentage() }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['referrals_remaining'] }} referral(s) remaining</p>
                        </div>

                        <!-- Payouts Usage -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-dollar-sign text-green-600"></i>
                                    <span class="text-sm font-semibold text-gray-900">Payouts This Month</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">
                                    {{ $subscription->payouts_used }} / {{ $subscription->package->max_payouts_per_month }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full transition-all" style="width: {{ $subscription->package->max_payouts_per_month > 0 ? min(100, ($subscription->payouts_used / $subscription->package->max_payouts_per_month) * 100) : 0 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['payouts_remaining'] }} payout(s) remaining</p>
                        </div>
                    </div>
                </div>

                <!-- Package Features -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Your Package Features</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                            <i class="fas fa-check-circle text-green-600"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $subscription->package->max_referrals }} Referrals</p>
                                <p class="text-xs text-gray-500">Maximum allowed</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 {{ $subscription->package->allow_rfqs ? 'bg-green-50' : 'bg-gray-50' }} rounded-lg">
                            <i class="fas fa-{{ $subscription->package->allow_rfqs ? 'check-circle text-green-600' : 'times-circle text-gray-400' }}"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">RFQ Access</p>
                                <p class="text-xs text-gray-500">{{ $subscription->package->allow_rfqs ? 'Enabled' : 'Not available' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 {{ $subscription->package->priority_support ? 'bg-green-50' : 'bg-gray-50' }} rounded-lg">
                            <i class="fas fa-{{ $subscription->package->priority_support ? 'check-circle text-green-600' : 'times-circle text-gray-400' }}"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Priority Support</p>
                                <p class="text-xs text-gray-500">{{ $subscription->package->priority_support ? 'Enabled' : 'Not available' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                            <i class="fas fa-percentage text-green-600"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $subscription->package->commission_rate }}% Commission</p>
                                <p class="text-xs text-gray-500">Earn rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>

                    <div class="space-y-3">
                        @if($subscription->isActive())
                            <a href="{{ route('agent.packages.index') }}" class="flex items-center gap-3 p-3 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg hover:bg-blue-100 transition-all">
                                <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-lg">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                                <div class="flex-1 text-left">
                                    <p class="font-medium text-gray-900 text-sm">Upgrade Plan</p>
                                    <p class="text-xs text-gray-600">Get more features</p>
                                </div>
                            </a>

                            <form action="{{ route('agent.packages.cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription?')">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 p-3 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-lg hover:bg-red-100 transition-all">
                                    <div class="flex items-center justify-center w-10 h-10 bg-red-600 text-white rounded-lg">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <div class="flex-1 text-left">
                                        <p class="font-medium text-gray-900 text-sm">Cancel Subscription</p>
                                        <p class="text-xs text-gray-600">Stop auto-renewal</p>
                                    </div>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('agent.packages.renew') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 p-3 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg hover:bg-green-100 transition-all">
                                    <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-lg">
                                        <i class="fas fa-sync-alt"></i>
                                    </div>
                                    <div class="flex-1 text-left">
                                        <p class="font-medium text-gray-900 text-sm">Renew Subscription</p>
                                        <p class="text-xs text-gray-600">Reactivate your plan</p>
                                    </div>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('agent.packages.history') }}" class="flex items-center gap-3 p-3 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-lg hover:bg-gray-100 transition-all">
                            <div class="flex items-center justify-center w-10 h-10 bg-gray-600 text-white rounded-lg">
                                <i class="fas fa-history"></i>
                            </div>
                            <div class="flex-1 text-left">
                                <p class="font-medium text-gray-900 text-sm">View History</p>
                                <p class="text-xs text-gray-600">Past subscriptions</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Payment Info</h3>

                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Payment Method</p>
                            <p class="text-sm font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $subscription->payment_method ?? 'N/A')) }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Transaction ID</p>
                            <p class="text-sm font-mono text-gray-900">{{ $subscription->transaction_id ?? 'N/A' }}</p>
                        </div>

                        @if($subscription->last_renewed_at)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Last Renewed</p>
                                <p class="text-sm text-gray-900">{{ $subscription->last_renewed_at->format('M d, Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-box-open text-2xl text-gray-300"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">No Active Subscription</h2>
            <p class="text-gray-600 mb-6">You don't have an active subscription yet</p>
            <a href="{{ route('agent.packages.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium">
                <i class="fas fa-box"></i>
                <span>Browse Packages</span>
            </a>
        </div>
    @endif
</div>
@endsection
