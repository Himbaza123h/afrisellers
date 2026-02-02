@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-2xl font-bold text-gray-900">Referral Details</h1>
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $referral->status_badge }}">
                    {{ ucfirst($referral->status) }}
                </span>
            </div>
            <p class="text-sm text-gray-500">Complete information about this referral</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('agent.referrals.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
            <a href="{{ route('agent.referrals.edit', $referral->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium shadow-sm">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Referral Information Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i>
                        Personal Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Full Name</label>
                            <p class="text-base font-semibold text-gray-900">{{ $referral->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                            <div class="flex items-center gap-2">
                                <a href="mailto:{{ $referral->email }}" class="text-base text-blue-600 hover:text-blue-700 font-medium">
                                    {{ $referral->email }}
                                </a>
                                <i class="fas fa-external-link-alt text-xs text-gray-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Phone Number</label>
                            <p class="text-base font-medium text-gray-900">
                                @if($referral->phone)
                                    <a href="tel:{{ $referral->phone }}" class="text-blue-600 hover:text-blue-700">
                                        {{ $referral->phone }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Not provided</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Referral Code</label>
                            <div class="flex items-center gap-2">
                                <span class="text-base font-mono font-bold text-gray-900">{{ $referral->referral_code }}</span>
                                <button onclick="copyCode('{{ $referral->referral_code }}')" class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-all text-xs font-medium" title="Copy code">
                                    <i class="fas fa-copy"></i>
                                    <span>Copy</span>
                                </button>
                            </div>
                        </div>

                        @if($referral->user)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-2">User Account</label>
                                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                    <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-lg">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-green-900">Registered User</p>
                                        <p class="text-xs text-green-700">This referral has created an account</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex px-3 py-1.5 text-sm font-semibold rounded-full {{ $referral->status_badge }}">
                                    {{ ucfirst($referral->status) }}
                                </span>
                                <form action="{{ route('agent.referrals.update-status', $referral->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-sm px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="pending" {{ $referral->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="active" {{ $referral->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $referral->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="rejected" {{ $referral->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    <span class="text-xs text-gray-500">Change status</span>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Card -->
            @if($referral->notes)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-yellow-100 border-b border-yellow-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-yellow-600"></i>
                            Notes
                        </h2>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $referral->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Commissions Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-dollar-sign text-green-600"></i>
                            Commission History
                        </h2>
                        <span class="px-3 py-1 bg-white rounded-full text-sm font-semibold text-gray-900 border border-green-200">
                            {{ $referral->commissions->count() }} commission(s)
                        </span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($referral->commissions as $commission)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $commission->created_at->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $commission->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-base font-bold text-gray-900">${{ number_format($commission->amount, 2) }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $commission->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($commission->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-700">{{ $commission->notes ?? '-' }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                                <i class="fas fa-dollar-sign text-xl text-gray-300"></i>
                                            </div>
                                            <p class="text-sm font-medium text-gray-900">No commissions yet</p>
                                            <p class="text-xs text-gray-500">Commissions will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="space-y-6">
            <!-- Commission Statistics -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-purple-100 border-b border-purple-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-purple-600"></i>
                        Commission Stats
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                        <div>
                            <p class="text-xs font-medium text-green-600 mb-1">Total Earned</p>
                            <p class="text-xl font-bold text-gray-900">${{ number_format($commissionStats['total'], 2) }}</p>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 bg-green-600 text-white rounded-lg">
                            <i class="fas fa-dollar-sign text-xl"></i>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div>
                            <p class="text-xs font-medium text-blue-600 mb-1">Paid Out</p>
                            <p class="text-xl font-bold text-gray-900">${{ number_format($commissionStats['paid'], 2) }}</p>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-600 text-white rounded-lg">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div>
                            <p class="text-xs font-medium text-yellow-600 mb-1">Pending</p>
                            <p class="text-xl font-bold text-gray-900">${{ number_format($commissionStats['pending'], 2) }}</p>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 bg-yellow-600 text-white rounded-lg">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <p class="text-xs font-medium text-gray-600 mb-1">Total Commissions</p>
                            <p class="text-xl font-bold text-gray-900">{{ $commissionStats['count'] }}</p>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 bg-gray-600 text-white rounded-lg">
                            <i class="fas fa-list text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-clock text-gray-600"></i>
                        Timeline
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Added On</label>
                        <p class="text-sm font-medium text-gray-900">{{ $referral->created_at->format('M d, Y h:i A') }}</p>
                        <p class="text-xs text-gray-500">{{ $referral->created_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-sm font-medium text-gray-900">{{ $referral->updated_at->format('M d, Y h:i A') }}</p>
                        <p class="text-xs text-gray-500">{{ $referral->updated_at->diffForHumans() }}</p>
                    </div>

                    @if($referral->registered_at)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Registered On</label>
                            <p class="text-sm font-medium text-gray-900">{{ $referral->registered_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $referral->registered_at->diffForHumans() }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-bolt text-blue-600"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('agent.referrals.edit', $referral->id) }}" class="flex items-center gap-3 p-3 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg hover:bg-blue-100 transition-all">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-lg">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 text-sm">Edit Referral</p>
                            <p class="text-xs text-gray-600">Update information</p>
                        </div>
                    </a>

                    <button onclick="copyCode('{{ $referral->referral_code }}')" class="w-full flex items-center gap-3 p-3 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg hover:bg-green-100 transition-all">
                        <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-lg">
                            <i class="fas fa-copy"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="font-medium text-gray-900 text-sm">Copy Code</p>
                            <p class="text-xs text-gray-600">Share referral code</p>
                        </div>
                    </button>

                    <form action="{{ route('agent.referrals.destroy', $referral->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this referral? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center gap-3 p-3 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-lg hover:bg-red-100 transition-all">
                            <div class="flex items-center justify-center w-10 h-10 bg-red-600 text-white rounded-lg">
                                <i class="fas fa-trash"></i>
                            </div>
                            <div class="flex-1 text-left">
                                <p class="font-medium text-gray-900 text-sm">Delete Referral</p>
                                <p class="text-xs text-gray-600">Remove permanently</p>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        // Show success message
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<div class="flex items-center gap-3 p-3"><div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-lg"><i class="fas fa-check"></i></div><div class="flex-1 text-left"><p class="font-medium text-gray-900 text-sm">Copied!</p><p class="text-xs text-gray-600">Code copied to clipboard</p></div></div>';

        setTimeout(function() {
            btn.innerHTML = originalHTML;
        }, 2000);
    }, function(err) {
        alert('Failed to copy code: ' + err);
    });
}

// Auto-hide success message after 5 seconds
setTimeout(function() {
    const alert = document.querySelector('.bg-green-50');
    if (alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.remove();
        }, 500);
    }
}, 5000);
</script>
@endsection
