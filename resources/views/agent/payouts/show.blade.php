@extends('layouts.home')

@section('page-content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('agent.payouts.index') }}"
               class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $payout->payout_number }}</h1>
                <p class="text-xs text-gray-500 mt-0.5">Payout Request Detail</p>
            </div>
        </div>
        <div class="flex gap-2">
            @php
                $statusConfig = [
                    'pending'    => 'bg-amber-100 text-amber-700',
                    'approved'   => 'bg-blue-100 text-blue-700',
                    'processing' => 'bg-indigo-100 text-indigo-700',
                    'paid'       => 'bg-green-100 text-green-700',
                    'rejected'   => 'bg-red-100 text-red-700',
                    'cancelled'  => 'bg-gray-100 text-gray-500',
                ];
                $sCls = $statusConfig[$payout->status] ?? 'bg-gray-100 text-gray-500';
            @endphp
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold {{ $sCls }}">
                <i class="fas fa-circle text-[7px]"></i>
                {{ ucfirst($payout->status) }}
            </span>

            @if($payout->isCancellable())
                <form action="{{ route('agent.payouts.cancel', $payout->id) }}" method="POST"
                      onsubmit="return confirm('Cancel this payout request?')">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 text-sm font-medium transition-colors">
                        <i class="fas fa-times"></i> Cancel Request
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5 flex-shrink-0"></i>
            <p class="text-sm text-green-900 font-medium flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Amount Card --}}
            <div class="bg-cyan-500 to-teal-600 rounded-xl p-5 text-white shadow-md">
                <p class="text-xs font-semibold text-cyan-100 uppercase tracking-wider mb-1">Requested Amount</p>
                <p class="text-4xl font-bold">
                    {{ $payout->currency }} {{ number_format($payout->amount, 2) }}
                </p>
                @php
                    $methodLabels = [
                        'bank_transfer' => ['fa-university',  'Bank Transfer'],
                        'mobile_money'  => ['fa-mobile-alt', 'Mobile Money'],
                        'paypal'        => ['fa-paypal',     'PayPal'],
                        'wise'          => ['fa-exchange-alt','Wise'],
                        'crypto'        => ['fa-coins',      'Crypto'],
                    ];
                    [$mIcon, $mLabel] = $methodLabels[$payout->payment_method] ?? ['fa-money-bill', ucfirst($payout->payment_method)];
                @endphp
                <p class="text-xs text-cyan-100 mt-2 flex items-center gap-1.5">
                    <i class="fas {{ $mIcon }}"></i> {{ $mLabel }}
                </p>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Timeline</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-paper-plane text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-800">Submitted</p>
                            <p class="text-xs text-gray-400">{{ $payout->created_at->format('M d, Y H:i') }}</p>
                            <p class="text-[10px] text-gray-300">{{ $payout->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    @if(in_array($payout->status, ['approved','processing','paid']))
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-thumbs-up text-indigo-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-800">Approved</p>
                                <p class="text-xs text-gray-400">Request approved by admin</p>
                            </div>
                        </div>
                    @endif

                    @if($payout->processed_at)
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-check-circle text-green-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-800">Processed</p>
                                <p class="text-xs text-gray-400">{{ $payout->processed_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($payout->status === 'rejected')
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-times-circle text-red-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-800">Rejected</p>
                                @if($payout->admin_notes)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $payout->admin_notes }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($payout->status === 'cancelled')
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-ban text-gray-400 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-800">Cancelled</p>
                                <p class="text-xs text-gray-400">Cancelled by you</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Request Details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Request Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Payout Number</dt>
                        <dd class="text-sm font-mono font-bold text-gray-900">{{ $payout->payout_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Amount</dt>
                        <dd class="text-sm font-bold text-gray-900">{{ $payout->currency }} {{ number_format($payout->amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Payment Method</dt>
                        <dd class="text-sm font-medium text-gray-800">{{ $mLabel }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Status</dt>
                        <dd>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $sCls }}">
                                <i class="fas fa-circle text-[6px]"></i> {{ ucfirst($payout->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Submitted</dt>
                        <dd class="text-sm text-gray-800">{{ $payout->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Processed</dt>
                        <dd class="text-sm text-gray-800">{{ $payout->processed_at ? $payout->processed_at->format('M d, Y H:i') : '—' }}</dd>
                    </div>
                </dl>

                @if($payout->notes)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <dt class="text-xs text-gray-400 font-medium mb-1">Your Notes</dt>
                        <dd class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">{{ $payout->notes }}</dd>
                    </div>
                @endif

                @if($payout->admin_notes)
                    <div class="mt-3">
                        <dt class="text-xs text-gray-400 font-medium mb-1">Admin Notes</dt>
                        <dd class="text-sm text-gray-700 bg-blue-50 border border-blue-100 rounded-lg p-3">
                            <i class="fas fa-comment-dots text-blue-400 mr-1"></i>
                            {{ $payout->admin_notes }}
                        </dd>
                    </div>
                @endif
            </div>

            {{-- Account Details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">
                    Payment Account Details
                </h3>
                @if($payout->account_details && count($payout->account_details))
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($payout->account_details as $key => $value)
                            @if($value)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <dt class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mb-0.5">
                                        {{ ucwords(str_replace('_', ' ', $key)) }}
                                    </dt>
                                    <dd class="text-sm font-medium text-gray-900 break-all
                                        {{ in_array($key, ['account_number','crypto_address','swift_code']) ? 'font-mono' : '' }}">
                                        {{ $value }}
                                    </dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                @else
                    <p class="text-sm text-gray-400">No account details recorded.</p>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
