@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">My Rewards</h1>
            <p class="mt-1 text-xs text-gray-500">Bonus credits earned by hitting performance targets</p>
        </div>
                <div class="flex flex-wrap gap-2 no-print">
<a href="{{ route('agent.commissions.index') }}"
   class="inline-flex items-center gap-2 px-3 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 text-sm font-medium shadow-sm">
    <i class="fas fa-arrow-left"></i> Back
</a>


        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 flex-1 font-medium">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="stat-card bg-amber-500 rounded-xl p-5 text-white shadow-md col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-amber-100 uppercase tracking-wider">Claimable</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($stats['pending_rewards'], 2) }}</p>
                    <p class="text-xs text-amber-100 mt-1">{{ $stats['pending_count'] }} reward(s) ready</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-gift text-xl text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-500 mb-1">Claimed</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format($stats['claimed_rewards'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">credits received</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs font-medium text-gray-500 mb-1">Total Earned</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total_rewards'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">all time</p>
        </div>
    </div>

    {{-- How it works --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
        <i class="fas fa-info-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
        <div class="text-sm text-blue-800">
            <p class="font-semibold mb-1">How Rewards Work</p>
            <p>When you reach a performance target within the set period, a reward is automatically created for you. Click <strong>Claim</strong> to add the bonus credits to your balance. Each target can only be rewarded once per period — your progress is tracked fairly.</p>
        </div>
    </div>

    {{-- Rewards Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-800">Reward History</h2>
            <span class="px-2 py-1 text-xs font-semibold text-amber-700 bg-amber-100 rounded-full">
                {{ $rewards->total() }} {{ Str::plural('reward', $rewards->total()) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Target</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Period</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bonus Credits</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($rewards as $reward)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-bullseye text-purple-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 capitalize">
                                            {{ ucfirst($reward->target?->target_type ?? '—') }} Target
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            Reach {{ number_format($reward->target?->target_amount ?? 0, 0) }} credits
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-mono text-gray-600">{{ $reward->period_key }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-base font-bold text-amber-600">
                                    +{{ number_format($reward->credits_awarded, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($reward->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        <i class="fas fa-circle text-[6px]"></i> Ready to Claim
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <i class="fas fa-check text-[8px]"></i> Claimed
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $reward->created_at->format('M d, Y') }}
                                @if($reward->claimed_at)
                                    <p class="text-[10px] text-gray-400">Claimed {{ $reward->claimed_at->diffForHumans() }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($reward->status === 'pending')
                                    <form action="{{ route('agent.rewards.claim', $reward->id) }}" method="POST"
                                          onsubmit="return confirm('Claim {{ number_format($reward->credits_awarded, 2) }} bonus credits?')">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 text-white rounded-lg text-xs font-semibold hover:bg-amber-600 shadow-sm">
                                            <i class="fas fa-hand-holding-usd"></i> Claim
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-trophy text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No rewards yet</p>
                                    <p class="text-xs text-gray-400 mt-1">Hit a performance target to earn bonus credits</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rewards->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500">
                    Showing {{ $rewards->firstItem() }}–{{ $rewards->lastItem() }} of {{ $rewards->total() }}
                </span>
                <div class="text-sm">{{ $rewards->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
