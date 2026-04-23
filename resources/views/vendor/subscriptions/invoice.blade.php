@extends('layouts.home')

@section('page-content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Action Buttons -->
    <div class="flex items-center justify-between print:hidden">
        <a href="{{ route('vendor.subscriptions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i>Back to Subscriptions
        </a>
        <button onclick="window.print()" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
            <i class="fas fa-print mr-1"></i>Print Invoice
        </button>
    </div>

    <!-- Invoice Container -->
    <div id="invoice-content" class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
        <!-- Invoice Header -->
        <div class="flex items-start justify-between pb-4 border-b border-gray-200">
            <div>
                <h1 class="text-lg font-bold text-gray-900 mb-2">INVOICE</h1>
                <div class="space-y-1 text-xs">
                    <div class="flex gap-2">
                        <span class="text-gray-500 w-16">Invoice #:</span>
                        <span class="text-gray-900 font-medium">#{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-500 w-16">Date:</span>
                        <span class="text-gray-900 font-medium">{{ $subscription->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-500 w-16">Status:</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $subscription->status === 'expired' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $subscription->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <img src="{{ asset('mainlogo.png') }}" alt="AfriSellers" class="h-8 ml-auto mb-2">
                <div class="text-xs text-gray-600 space-y-0.5">
                    <div>Marketplace Platform</div>
                    <div>{{ config('app.email_address') }}</div>
                    <div>{{ config('app.phone_number') }}</div>
                </div>
            </div>
        </div>

        <!-- Bill To & Payment Information -->
        <div class="grid grid-cols-2 gap-4 py-4 border-b border-gray-200">
            <div>
                <h3 class="text-xs font-semibold text-gray-700 uppercase mb-2">Bill To:</h3>
                <div class="space-y-0.5 text-xs">
                    <div class="text-gray-900 font-semibold text-sm">{{ $subscription->seller->name }}</div>
                    <div class="text-gray-600">{{ $subscription->seller->email }}</div>
                    @if($subscription->seller->vendor && $subscription->seller->vendor->businessProfile)
                        <div class="text-gray-600 pt-1">{{ $subscription->seller->vendor->businessProfile->business_name }}</div>
                        @if($subscription->seller->vendor->businessProfile->business_address)
                            <div class="text-gray-600">{{ $subscription->seller->vendor->businessProfile->business_address }}</div>
                        @endif
                    @endif
                </div>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-700 uppercase mb-2">Payment Information:</h3>
                <div class="space-y-1 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="text-gray-900 font-medium">Credit Card</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transaction ID:</span>
                        <span class="text-gray-900 font-medium">#TXN{{ $subscription->id }}{{ now()->format('Ymd') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Date:</span>
                        <span class="text-gray-900 font-medium">{{ $subscription->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="py-4">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 border-y border-gray-200">
                        <th class="px-3 py-2 text-left font-semibold text-gray-700 uppercase">Description</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-700 uppercase">Duration</th>
                        <th class="px-3 py-2 text-right font-semibold text-gray-700 uppercase">Unit Price</th>
                        <th class="px-3 py-2 text-right font-semibold text-gray-700 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200">
                        <td class="px-3 py-3">
                            <div class="font-semibold text-gray-900 text-sm mb-0.5">{{ $subscription->plan->name }}</div>
                            <div class="text-gray-600">Subscription Period</div>
                            <div class="text-gray-500 mt-0.5">{{ $subscription->starts_at->format('M d, Y') }} - {{ $subscription->ends_at->format('M d, Y') }}</div>
                            @if($subscription->is_trial)
                                <span class="inline-block px-2 py-0.5 bg-purple-100 text-purple-800 rounded-full mt-1">Trial Period</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center text-gray-900">{{ $subscription->plan->duration_days }} days</td>
                        <td class="px-3 py-3 text-right font-semibold text-gray-900">${{ number_format($subscription->plan->price, 2) }}</td>
                        <td class="px-3 py-3 text-right font-semibold text-gray-900">${{ number_format($subscription->plan->price, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Plan Features -->
        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mb-4">
            <h3 class="text-xs font-semibold text-gray-900 mb-2">Plan Features Included:</h3>
            <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                @foreach($subscription->plan->features as $feature)
                    <div class="flex items-start gap-1.5 text-xs">
                        <i class="fas fa-check-circle text-green-600 mt-0.5" style="font-size: 10px;"></i>
                        <span class="text-gray-700">
                            <span class="font-medium">{{ ucwords(str_replace('_', ' ', $feature->feature_key)) }}:</span>
                            {{ $feature->feature_value }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Totals -->
        <div class="flex justify-end mb-4">
            <div class="w-64">
                <div class="space-y-1 text-xs">
                    <div class="flex justify-between py-1">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="text-gray-900 font-medium">${{ number_format($subscription->plan->price, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-gray-600">Tax (0%):</span>
                        <span class="text-gray-900 font-medium">$0.00</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-gray-600">Discount:</span>
                        <span class="text-gray-900 font-medium">$0.00</span>
                    </div>
                    <div class="border-t border-gray-200 my-1"></div>
                    <div class="flex justify-between py-1">
                        <span class="text-sm font-bold text-gray-900">Total:</span>
                        <span class="text-base font-bold text-gray-900">${{ number_format($subscription->plan->price, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Notes -->
        <div class="pt-4 border-t border-gray-200">
            <h3 class="text-xs font-semibold text-gray-900 mb-1">Notes:</h3>
            <p class="text-xs text-gray-600 mb-3">
                Thank you for your subscription! This invoice confirms your {{ $subscription->plan->name }} membership.
                Your subscription is {{ $subscription->auto_renew ? 'set to auto-renew' : 'not set to auto-renew' }} on {{ $subscription->ends_at->format('M d, Y') }}.
            </p>
        </div>

        <!-- Footer -->
        <div class="pt-3 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-500">This is a computer-generated invoice and does not require a signature.</p>
            <p class="text-xs text-gray-500 mt-0.5">© {{ date('Y') }} Afrisellers. All rights reserved.</p>
        </div>
    </div>
</div>

<style>
    @media print {
        /* Hide everything */
        body * {
            visibility: hidden !important;
        }

        /* Show only invoice content */
        #invoice-content,
        #invoice-content * {
            visibility: visible !important;
        }

        /* Position invoice at top-left corner */
        #invoice-content {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 20px !important;
            background: white !important;
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
        }

        /* Single page optimization */
        @page {
            margin: 0.5cm;
            size: A4;
        }

        /* Hide print button and back link */
        .print\:hidden {
            display: none !important;
        }

        /* Remove any background colors for print */
        body {
            background: white !important;
        }

        /* Ensure text is black for printing */
        * {
            color-adjust: exact !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endsection
