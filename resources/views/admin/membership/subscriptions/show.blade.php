@extends('layouts.home')

@section('page-content')
<div class="space-y-6 max-w-5xl">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.memberships.subscriptions.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Subscription Details</h1>
            <p class="text-sm text-gray-500 mt-1">Manage subscription and view details</p>
        </div>
        <span class="px-4 py-2 rounded-full text-sm font-medium
            {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
            {{ $subscription->status === 'trial' ? 'bg-purple-100 text-purple-800' : '' }}
            {{ $subscription->status === 'expired' ? 'bg-orange-100 text-orange-800' : '' }}
            {{ $subscription->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
            {{ ucfirst($subscription->status) }}
        </span>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
        </div>
    @endif

    <!-- User & Plan Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Seller Info -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-blue-600"></i>
                Seller Information
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Name</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $subscription->seller->user->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Email</p>
                    <p class="text-sm text-gray-900">{{ $subscription->seller->user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Business Name</p>
                    <p class="text-sm text-gray-900">{{ $subscription->seller->businessProfile->business_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Member Since</p>
                    <p class="text-sm text-gray-900">{{ $subscription->seller->user->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Plan Info -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-crown text-purple-600"></i>
                Plan Information
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Plan Name</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $subscription->plan->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Price</p>
                    <p class="text-sm text-gray-900">${{ number_format($subscription->plan->price, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Duration</p>
                    <p class="text-sm text-gray-900">{{ $subscription->plan->duration_days }} days</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Subscription Type</p>
                    @if($subscription->is_trial)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Trial Period</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Paid Subscription</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Timeline -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-calendar text-green-600"></i>
            Subscription Timeline
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-gray-500 mb-2">Start Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ $subscription->starts_at->format('M d, Y H:i') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $subscription->starts_at->diffForHumans() }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-2">End Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ $subscription->ends_at->format('M d, Y H:i') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $subscription->ends_at->diffForHumans() }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-2">Days Remaining</p>
                @php
                    $daysLeft = round($subscription->daysRemaining());
                @endphp
                @if($daysLeft > 0)
                    <p class="text-2xl font-bold text-green-600">{{ $daysLeft }}</p>
                    <p class="text-xs text-gray-500 mt-1">days left</p>
                @else
                    <p class="text-2xl font-bold text-red-600">0</p>
                    <p class="text-xs text-red-500 mt-1">Expired</p>
                @endif
            </div>
        </div>

        <!-- Progress Bar -->
        @php
            $totalDays = $subscription->starts_at->diffInDays($subscription->ends_at);
            $daysUsed = $subscription->starts_at->diffInDays(now());
            $percentage = $totalDays > 0 ? min(($daysUsed / $totalDays) * 100, 100) : 100;
        @endphp
        <div class="mt-6">
            <div class="flex justify-between text-xs text-gray-600 mb-2">
                <span>Progress</span>
                <span>{{ number_format($percentage, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-green-500 to-blue-600 h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
            </div>
        </div>
    </div>

    <!-- Plan Features -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-list text-blue-600"></i>
            Plan Features
        </h3>
        @if($subscription->plan->features->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($subscription->plan->features as $feature)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <code class="text-xs font-mono text-blue-600">{{ $feature->feature_key }}</code>
                        <p class="text-sm font-semibold text-gray-900 mt-2">{{ $feature->feature_value }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500">No features configured for this plan</p>
        @endif
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
        <div class="flex flex-wrap gap-3">
            @if($subscription->status === 'active')
                <!-- Renew -->
                <button onclick="showRenewModal()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    <i class="fas fa-redo"></i>
                    <span>Renew Subscription</span>
                </button>

                <!-- Change Plan -->
                <button onclick="showChangePlanModal()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Change Plan</span>
                </button>

                <!-- Cancel -->
                <form action="{{ route('admin.memberships.subscriptions.cancel', $subscription) }}" method="POST" onsubmit="return confirm('Cancel this subscription?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                        <i class="fas fa-ban"></i>
                        <span>Cancel Subscription</span>
                    </button>
                </form>
            @elseif($subscription->status === 'expired')
                <button onclick="showRenewModal()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    <i class="fas fa-redo"></i>
                    <span>Reactivate Subscription</span>
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Renew Modal -->
<div id="renewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Renew Subscription</h3>
        <form action="{{ route('admin.memberships.subscriptions.renew', $subscription) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Days)</label>
                <input type="number" name="duration_days" value="{{ $subscription->plan->duration_days }}" min="1" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRenewModal()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Renew</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Plan Modal -->
<div id="changePlanModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Change Plan</h3>
        <form action="{{ route('admin.memberships.subscriptions.change-plan', $subscription) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select New Plan</label>
                <select name="plan_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                    @foreach(\App\Models\MembershipPlan::where('is_active', true)->get() as $plan)
                        <option value="{{ $plan->id }}" {{ $plan->id == $subscription->plan_id ? 'selected' : '' }}>
                            {{ $plan->name }} - ${{ number_format($plan->price, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeChangePlanModal()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Change</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRenewModal() {
    document.getElementById('renewModal').classList.remove('hidden');
}
function closeRenewModal() {
    document.getElementById('renewModal').classList.add('hidden');
}
function showChangePlanModal() {
    document.getElementById('changePlanModal').classList.remove('hidden');
}
function closeChangePlanModal() {
    document.getElementById('changePlanModal').classList.add('hidden');
}
</script>
@endsection
