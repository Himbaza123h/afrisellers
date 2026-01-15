@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">My Subscription</h1>
        <p class="mt-1 text-sm text-gray-500">Manage your membership plan and billing</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Spent</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_spent'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Days Remaining</p>
                    <p class="text-2xl font-bold text-gray-900">{{ round($stats['active_days']) }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Subscriptions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_subscriptions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-history text-xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
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

    <!-- Current Subscription -->
    @if($currentSubscription)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-purple-600 to-blue-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $currentSubscription->plan->name }}</h2>
                        <p class="text-purple-100 mt-1">Your current membership plan</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold">${{ number_format($currentSubscription->plan->price, 2) }}</p>
                        <p class="text-purple-100 text-sm">per {{ $currentSubscription->plan->duration_days }} days</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Start Date</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $currentSubscription->starts_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">End Date</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $currentSubscription->ends_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $currentSubscription->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($currentSubscription->status) }}
                        </span>
                    </div>
                </div>

                <!-- Progress Bar -->
                @php
                    $totalDays = $currentSubscription->starts_at->diffInDays($currentSubscription->ends_at);
                    $daysUsed = $currentSubscription->starts_at->diffInDays(now());
                    $percentage = $totalDays > 0 ? min(($daysUsed / $totalDays) * 100, 100) : 100;
                @endphp
                <div class="mb-6">
                    <div class="flex justify-between text-xs text-gray-600 mb-2">
                        <span>Subscription Progress</span>
                        <span>{{ number_format($percentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-purple-500 to-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>

                <!-- Plan Features -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Plan Features</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($currentSubscription->plan->features as $feature)
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-600 text-sm"></i>
                                <span class="text-sm text-gray-700"><code class="text-xs bg-gray-100 px-2 py-0.5 rounded">{{ ucwords(str_replace('_', ' ', $feature->feature_key)) }}</code>: {{ $feature->feature_value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-3 pt-4 border-t">
                    <form action="{{ route('vendor.subscriptions.renew') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                            <i class="fas fa-redo"></i>
                            <span>Renew Now</span>
                        </button>
                    </form>

                    <button onclick="showUpgradeModal()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        <i class="fas fa-arrow-up"></i>
                        <span>Upgrade Plan</span>
                    </button>

                    <form action="{{ route('vendor.subscriptions.toggle-auto-renew') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                            <i class="fas fa-{{ $currentSubscription->auto_renew ? 'pause' : 'play' }}-circle"></i>
                            <span>{{ $currentSubscription->auto_renew ? 'Disable' : 'Enable' }} Auto-Renew</span>
                        </button>
                    </form>

                    <a href="{{ route('vendor.subscriptions.invoice', $currentSubscription) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                        <i class="fas fa-file-invoice"></i>
                        <span>View Invoice</span>
                    </a>

                    @if($currentSubscription->status === 'active')
                        <form action="{{ route('vendor.subscriptions.cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription?');">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 font-medium">
                                <i class="fas fa-ban"></i>
                                <span>Cancel Subscription</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @else
        <!-- No Active Subscription - Show Available Plans -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                <div>
                    <h3 class="text-lg font-semibold text-yellow-900 mb-1">No Active Subscription</h3>
                    <p class="text-sm text-yellow-700">You don't have an active subscription. Choose a plan below to get started!</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Available Plans -->
    @if(!$currentSubscription || $currentSubscription->status !== 'active')
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4">Available Plans</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($availablePlans as $plan)
                    <div class="bg-white rounded-xl border-2 {{ $plan->slug === 'pro' ? 'border-purple-300 shadow-lg' : 'border-gray-200' }} overflow-hidden">
                        @if($plan->slug === 'pro')
                            <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white text-center py-2 text-sm font-semibold">
                                MOST POPULAR
                            </div>
                        @endif
                        <div class="p-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                            <div class="flex items-baseline mb-6">
                                <span class="text-4xl font-bold text-gray-900">${{ number_format($plan->price, 2) }}</span>
                                <span class="text-gray-500 ml-2">/ {{ $plan->duration_days }} days</span>
                            </div>

                            <ul class="space-y-3 mb-6">
                                @foreach($plan->features as $feature)
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                                        <span class="text-sm text-gray-700"><strong>{{ ucwords(str_replace('_', ' ', $feature->feature_key)) }}:</strong> {{ $feature->feature_value }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <form action="{{ route('vendor.subscriptions.subscribe', $plan) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-3 {{ $plan->slug === 'pro' ? 'bg-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700' : 'bg-[#ff0808] hover:bg-[#e60707]' }} text-white rounded-lg font-semibold transition-all">
                                    Subscribe Now
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Subscription History -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Subscription History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Plan</th>
                        <th class="px-6 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Period</th>
                        <th class="px-6 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Price</th>
                        <th class="px-6 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($subscriptionHistory as $subscription)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-gray-900">{{ $subscription->plan->name }}</span>
                                @if($subscription->is_trial)
                                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Trial</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-600">
                                    <div>{{ $subscription->starts_at->format('M d, Y') }}</div>
                                    <div>{{ $subscription->ends_at->format('M d, Y') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-gray-900">${{ number_format($subscription->plan->price, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $subscription->status === 'expired' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $subscription->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('vendor.subscriptions.invoice', $subscription) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No subscription history found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscriptionHistory->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $subscriptionHistory->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Upgrade Modal -->
<div id="upgradeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Upgrade Your Plan</h3>
        <form action="{{ route('vendor.subscriptions.upgrade') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select New Plan</label>
                <select name="plan_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                    @foreach($availablePlans as $plan)
                        @if(!$currentSubscription || $plan->id != $currentSubscription->plan_id)
                            <option value="{{ $plan->id }}">{{ $plan->name }} - ${{ number_format($plan->price, 2) }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeUpgradeModal()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Upgrade</button>
            </div>
        </form>
    </div>
</div>

<script>
function showUpgradeModal() {
    document.getElementById('upgradeModal').classList.remove('hidden');
}
function closeUpgradeModal() {
    document.getElementById('upgradeModal').classList.add('hidden');
}
</script>
@endsection
