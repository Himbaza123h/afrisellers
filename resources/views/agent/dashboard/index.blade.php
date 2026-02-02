@extends('layouts.home')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .tab-content { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Agent Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Welcome back, {{ auth()->user()->name }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-br from-green-50 to-green-100 border border-green-200 text-green-700 rounded-lg font-medium shadow-sm">
                <span class="w-2 h-2 bg-green-600 rounded-full animate-pulse"></span>
                <span class="text-sm font-semibold">Agent Active</span>
            </div>
            <button onclick="window.open('{{ route('agent.dashboard.print') }}' + window.location.search, '_blank')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('agent.dashboard.home') }}" class="flex flex-wrap items-center gap-3">
            <label class="text-sm font-medium text-gray-700">Filter by:</label>

            <!-- Period Buttons -->
            <div class="flex gap-2">
                <button type="submit" name="filter" value="weekly" class="px-3 py-1.5 text-xs font-medium {{ (!request('filter') || request('filter') == 'weekly') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all">
                    Weekly
                </button>
                <button type="submit" name="filter" value="monthly" class="px-3 py-1.5 text-xs font-medium {{ request('filter') == 'monthly' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all">
                    Monthly
                </button>
                <button type="submit" name="filter" value="yearly" class="px-3 py-1.5 text-xs font-medium {{ request('filter') == 'yearly' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }} rounded-lg transition-all">
                    Yearly
                </button>
            </div>

            <!-- Custom Date Range -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">or</span>
                <div class="relative">
                    <input type="text" id="dateRangePicker" name="date_range" value="{{ request('date_range') }}" readonly placeholder="Custom date range" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-64 cursor-pointer bg-white text-sm">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none mt-2"></i>
                </div>
                <input type="hidden" name="filter" value="custom" id="customFilterInput">
            </div>

            @if(request()->hasAny(['filter', 'date_range']))
                <a href="{{ route('agent.dashboard.home') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-xs transition-all">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 no-print">
        <button onclick="switchTab('overview')" id="tab-overview" class="tab-button px-4 py-2 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 transition-colors">
            <i class="fas fa-chart-line mr-2"></i> Overview
        </button>
        <button onclick="switchTab('referrals')" id="tab-referrals" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-users mr-2"></i> Referrals
        </button>
        <button onclick="switchTab('commissions')" id="tab-commissions" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-dollar-sign mr-2"></i> Commissions
        </button>
        <button onclick="switchTab('analytics')" id="tab-analytics" class="tab-button px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-chart-bar mr-2"></i> Analytics
        </button>
    </div>

    <!-- Overview Tab Content (Default) -->
    <div id="tab-overview-content" class="tab-content">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Referrals -->
            <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Referrals</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($totalReferrals) }}</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-check-circle mr-1 text-[10px]"></i> {{ $activeReferrals }} Active
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Total Commissions -->
            <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Commissions</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($totalCommissions, 2) }}</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $commissionPercentage >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-arrow-{{ $commissionPercentage >= 0 ? 'up' : 'down' }} mr-1 text-[10px]"></i> {{ abs($commissionPercentage) }}%
                            </span>
                            <span class="text-xs text-gray-500">vs previous period</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                        <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Period Commissions -->
            <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Period Commissions</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($periodCommissions, 2) }}</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $commissionPercentage >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-arrow-{{ $commissionPercentage >= 0 ? 'up' : 'down' }} mr-1 text-[10px]"></i> {{ abs($commissionPercentage) }}%
                            </span>
                            <span class="text-xs text-gray-500">vs previous period</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                        <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Commissions -->
            <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Pending Commissions</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($pendingCommissions, 2) }}</p>
                        <div class="mt-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pendingCommissions > 0 ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                <i class="fas fa-{{ $pendingCommissions > 0 ? 'clock' : 'check-circle' }} mr-1 text-[10px]"></i> {{ $pendingCommissions > 0 ? 'Pending Payment' : 'All Paid' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl">
                        <i class="fas fa-clock text-2xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Performance Chart -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Performance Overview</h3>
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium">
                        {{ ucfirst(request('filter', 'weekly')) }} View
                    </span>
                </div>
                <div class="h-[320px]">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <!-- Recent Referrals -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Recent Referrals</h3>
                    <a href="{{ route('agent.referrals.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View All</a>
                </div>
                <div class="space-y-4">
                    @forelse($recentReferrals as $referral)
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                        <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-{{ $referral['color'] }}-50 to-{{ $referral['color'] }}-100 rounded-lg flex-shrink-0">
                            <i class="fas fa-user text-{{ $referral['color'] }}-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $referral['name'] }}</p>
                            <p class="text-xs text-gray-500 mb-1">{{ $referral['email'] }}</p>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs text-gray-500">{{ $referral['date'] }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $referral['color'] }}-100 text-{{ $referral['color'] }}-800">
                                    {{ $referral['status'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-users text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-900 mb-1">No referrals yet</p>
                        <p class="text-xs text-gray-500">Referrals will appear here</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Referral Statistics -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Referral Status Breakdown</h3>
                <a href="{{ route('agent.referrals.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View All Referrals</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($referralStats as $stat)
                <div class="p-5 bg-gradient-to-br from-{{ $stat['color'] }}-50 to-white rounded-xl border border-{{ $stat['color'] }}-100">
                    <div class="mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-bold text-gray-900">{{ $stat['status'] }}</p>
                            <div class="flex items-center justify-center w-10 h-10 bg-{{ $stat['color'] }}-100 rounded-lg">
                                <i class="fas fa-{{ $stat['icon'] }} text-{{ $stat['color'] }}-600"></i>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stat['count'] }}</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                        <div class="bg-{{ $stat['color'] }}-600 h-2 rounded-full transition-all duration-500" style="width: {{ $stat['percentage'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600">{{ $stat['percentage'] }}% of total referrals</p>
                </div>
                @empty
                <div class="col-span-full flex flex-col items-center justify-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-users text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-900 mb-1">No referral data yet</p>
                    <p class="text-xs text-gray-500">Start referring to see statistics</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Commission History -->
        <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm mt-6">
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Recent Commission History</h3>
                    <a href="{{ route('agent.commissions.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View All</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Referral</th>
                            <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Amount</th>
                            <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Date</th>
                            <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($commissionHistory as $commission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-{{ $commission['color'] }}-50 to-{{ $commission['color'] }}-100 rounded-lg">
                                        <i class="fas fa-user text-{{ $commission['color'] }}-600"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $commission['referral'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900">${{ number_format($commission['amount'], 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-{{ $commission['color'] }}-100 text-{{ $commission['color'] }}-800">
                                    {{ $commission['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700">{{ $commission['date'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="#" class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                                    View <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-dollar-sign text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">No commissions yet</p>
                                    <p class="text-xs text-gray-500 mt-1">Commissions will appear here</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Referrals Tab Content (Hidden by default) -->
    <div id="tab-referrals-content" class="tab-content hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Referrals Summary</h3>
                <div class="flex gap-2">
                    <a href="{{ route('agent.referrals.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-sm font-medium">
                        <i class="fas fa-plus"></i>
                        <span>Add Referral</span>
                    </a>
                    <a href="{{ route('agent.referrals.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all text-sm font-medium">
                        <i class="fas fa-list"></i>
                        <span>Manage Referrals</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="p-5 bg-blue-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                            <i class="fas fa-users text-xl text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-600">Total Referrals</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalReferrals) }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-green-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-xl text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-600">Active Referrals</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($activeReferrals) }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-orange-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-lg">
                            <i class="fas fa-clock text-xl text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-orange-600">Pending Referrals</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($pendingReferrals) }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-purple-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                            <i class="fas fa-chart-line text-xl text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-purple-600">Conversion Rate</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalReferrals > 0 ? number_format(($activeReferrals / $totalReferrals) * 100, 1) : '0.0' }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referral Status Chart -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Referral Status Distribution</h4>
                    <div class="h-64">
                        <canvas id="referralStatusChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Quick Actions</h4>
                    <div class="space-y-3">
                        <a href="{{ route('agent.referrals.create') }}" class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl hover:bg-blue-100 transition-all">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-lg">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Add New Referral</p>
                                <p class="text-xs text-gray-600">Refer someone to earn commission</p>
                            </div>
                        </a>

                        <a href="{{ route('agent.referrals.index') }}" class="flex items-center gap-3 p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl hover:bg-green-100 transition-all">
                            <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-lg">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">Manage Referrals</p>
                                <p class="text-xs text-gray-600">View and track all referrals</p>
                            </div>
                        </a>

                        <a href="{{ route('agent.commissions.index') }}" class="flex items-center gap-3 p-4 bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 rounded-xl hover:bg-purple-100 transition-all">
                            <div class="flex items-center justify-center w-10 h-10 bg-purple-600 text-white rounded-lg">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">View Commissions</p>
                                <p class="text-xs text-gray-600">Check your earnings and payouts</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commissions Tab Content (Hidden by default) -->
    <div id="tab-commissions-content" class="tab-content hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Commissions Summary</h3>
                <div class="flex gap-2">
                    <a href="{{ route('agent.commissions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all text-sm font-medium">
                        <i class="fas fa-list"></i>
                        <span>View All Commissions</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="p-5 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
                    <p class="text-sm font-medium text-green-600 mb-1">Total Earned</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($totalCommissions, 2) }}</p>
                    <p class="text-xs text-gray-600 mt-2">All time earnings</p>
                </div>

                <div class="p-5 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                    <p class="text-sm font-medium text-blue-600 mb-1">Paid Out</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($paidCommissions, 2) }}</p>
                    <p class="text-xs text-gray-600 mt-2">Successfully received</p>
                </div>

                <div class="p-5 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border border-orange-200">
                    <p class="text-sm font-medium text-orange-600 mb-1">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($pendingCommissions, 2) }}</p>
                    <p class="text-xs text-gray-600 mt-2">Awaiting payment</p>
                </div>

                <div class="p-5 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
                    <p class="text-sm font-medium text-purple-600 mb-1">This Period</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($periodCommissions, 2) }}</p>
                    <p class="text-xs text-gray-600 mt-2">Current period earnings</p>
                </div>
            </div>

            <!-- Commission History Table -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h4 class="font-semibold text-gray-700">Recent Commissions</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referral</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($commissionHistory as $commission)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $commission['referral'] }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">${{ number_format($commission['amount'], 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-{{ $commission['color'] }}-100 text-{{ $commission['color'] }}-800">
                                        {{ $commission['status'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $commission['date'] }}</td>
                                <td class="px-6 py-4">
                                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-dollar-sign text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">No commissions found</p>
                                        <p class="text-xs text-gray-500 mt-1">Commissions will appear here</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Tab Content (Hidden by default) -->
    <div id="tab-analytics-content" class="tab-content hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Analytics Overview</h3>
                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium">
                    {{ ucfirst(request('filter', 'weekly')) }} Period
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Referrals Chart -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Referral Trend</h4>
                    <div class="h-64">
                        <canvas id="analyticsReferralsChart"></canvas>
                    </div>
                </div>

                <!-- Commissions Chart -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Commission Breakdown</h4>
                    <div class="h-64">
                        <canvas id="commissionsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Analytics Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                <div class="p-5 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                    <p class="text-sm font-medium text-blue-600 mb-1">Avg. Commission</p>
                    <p class="text-2xl font-bold text-gray-900">
                        ${{ $totalReferrals > 0 ? number_format($totalCommissions / $totalReferrals, 2) : '0.00' }}
                    </p>
                </div>

                <div class="p-5 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
                    <p class="text-sm font-medium text-green-600 mb-1">Conversion Rate</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $totalReferrals > 0 ? number_format(($activeReferrals / $totalReferrals) * 100, 1) : '0.0' }}%
                    </p>
                </div>

                <div class="p-5 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
                    <p class="text-sm font-medium text-purple-600 mb-1">Active Referrals</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($activeReferrals) }}</p>
                </div>

                <div class="p-5 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border border-orange-200">
                    <p class="text-sm font-medium text-orange-600 mb-1">Growth Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $commissionPercentage >= 0 ? '+' : '' }}{{ $commissionPercentage }}%</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize Flatpickr for custom date range
    flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        showMonths: 2,
        locale: { rangeSeparator: " to " },
        onClose: function(dates, str, inst) {
            if (dates.length === 2) {
                document.getElementById('customFilterInput').value = 'custom';
                inst.element.closest('form').submit();
            }
        }
    });

    // Tab Switching Function
    function switchTab(tabName) {
        // Remove active state from all tabs
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            btn.classList.add('text-gray-600');
        });

        // Add active state to selected tab
        const activeTab = document.getElementById(`tab-${tabName}`);
        activeTab.classList.remove('text-gray-600');
        activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Show selected tab content
        document.getElementById(`tab-${tabName}-content`).classList.remove('hidden');
    }

    // Initialize with Overview tab active
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('overview');
        initializeCharts();
    });

    // Initialize Charts
    function initializeCharts() {
        // Main Performance Chart
        const performanceCtx = document.getElementById('performanceChart');
        if (performanceCtx) {
            new Chart(performanceCtx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Referrals',
                        data: @json($chartData['referrals']),
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderColor: '#3b82f6',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }, {
                        label: 'Commissions ($)',
                        data: @json($chartData['commissions']),
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderColor: '#22c55e',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#22c55e',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 12 },
                                boxWidth: 40,
                                boxHeight: 12,
                                usePointStyle: true
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Referral Status Chart
        const referralStatusCtx = document.getElementById('referralStatusChart');
        if (referralStatusCtx) {
            const statusLabels = @json(array_column($referralStats, 'status'));
            const statusCounts = @json(array_column($referralStats, 'count'));
            const colors = ['#22c55e', '#f59e0b', '#ef4444'];

            new Chart(referralStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusCounts,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Analytics Referrals Chart
        const analyticsCtx = document.getElementById('analyticsReferralsChart');
        if (analyticsCtx) {
            new Chart(analyticsCtx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Referrals',
                        data: @json($chartData['referrals']),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: '#3b82f6',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Commissions Chart
        const commissionsCtx = document.getElementById('commissionsChart');
        if (commissionsCtx) {
            new Chart(commissionsCtx, {
                type: 'pie',
                data: {
                    labels: ['Paid', 'Pending'],
                    datasets: [{
                        data: [{{ $paidCommissions }}, {{ $pendingCommissions }}],
                        backgroundColor: ['#22c55e', '#f59e0b'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
    }
</script>
@endpush
@endsection
