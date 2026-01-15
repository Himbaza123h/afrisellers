@extends('layouts.home')

@section('page-content')
<div class="space-y-4">
    <!-- Action Buttons -->
    <div class="flex items-center justify-between print:hidden" style="max-width: 60%;">
        <a href="{{ route('vendor.subscriptions.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Subscriptions
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            <i class="fas fa-print mr-2"></i>Print Invoice
        </button>
    </div>

    <!-- Invoice Container -->
    <div id="invoice-content" class="bg-white rounded-xl border border-gray-200 shadow-sm p-8" style="max-width: 60%; min-height: calc(100vh - 100px);">
        <table class="w-full border-collapse" style="height: 100%;">
            <!-- Invoice Header -->
            <tr>
                <td colspan="2" class="pb-5 border-b">
                    <table class="w-full">
                        <tr>
                            <td class="align-top w-1/2">
                                <h1 class="text-2xl font-bold text-gray-900 mb-2">INVOICE</h1>
                                <table class="text-sm">
                                    <tr>
                                        <td class="text-gray-600 py-1">Invoice #:</td>
                                        <td class="text-gray-900 font-medium pl-3">#{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 py-1">Date:</td>
                                        <td class="text-gray-900 font-medium pl-3">{{ $subscription->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 py-1">Status:</td>
                                        <td class="pl-3">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                                {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $subscription->status === 'expired' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $subscription->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="align-top text-right w-1/2">
                                <img src="https://afrisellers.com/public/uploads/all/rcIW6v7SfbxlCbrTIBU6CXQNggsQbKVO1a8vXheE.png"
                                    alt="AfriSellers" class="h-10 ml-auto mb-2">
                                <table class="ml-auto text-sm">
                                    <tr>
                                        <td class="text-gray-600 py-1">Marketplace Platform</td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 py-1">{{ config('app.email_address') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 py-1">{{ config('app.phone_number') }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Bill To & Payment Information -->
            <tr>
                <td colspan="2" class="pt-5 pb-5">
                    <table class="w-full">
                        <tr>
                            <td class="align-top w-1/2 pr-4">
                                <h3 class="text-xs font-semibold text-gray-700 uppercase mb-2">Bill To:</h3>
                                <table class="text-sm">
                                    <tr>
                                        <td class="text-gray-900 font-semibold text-base py-1">{{ $subscription->seller->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 py-1">{{ $subscription->seller->email }}</td>
                                    </tr>
                                    @if($subscription->seller->vendor && $subscription->seller->vendor->businessProfile)
                                        <tr>
                                            <td class="text-gray-600 py-1 pt-2">{{ $subscription->seller->vendor->businessProfile->business_name }}</td>
                                        </tr>
                                        @if($subscription->seller->vendor->businessProfile->business_address)
                                            <tr>
                                                <td class="text-gray-600 py-1">{{ $subscription->seller->vendor->businessProfile->business_address }}</td>
                                            </tr>
                                        @endif
                                    @endif
                                </table>
                            </td>
                            <td class="align-top w-1/2 pl-4 border-l">
                                <h3 class="text-xs font-semibold text-gray-700 uppercase mb-2">Payment Information:</h3>
                                <table class="w-full text-sm">
                                    <tr>
                                        <td class="text-gray-600 py-1">Payment Method:</td>
                                        <td class="text-gray-900 font-medium text-right">Credit Card</td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 py-1">Transaction ID:</td>
                                        <td class="text-gray-900 font-medium text-right">#TXN{{ $subscription->id }}{{ now()->format('Ymd') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-600 py-1">Payment Date:</td>
                                        <td class="text-gray-900 font-medium text-right">{{ $subscription->created_at->format('M d, Y') }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Invoice Items -->
            <tr>
                <td colspan="2" class="pt-5 pb-5">
                    <table class="w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-b">Description</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">Duration</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase border-b">Unit Price</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase border-b">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4 py-3 align-top">
                                    <table>
                                        <tr>
                                            <td class="font-semibold text-gray-900 text-sm">{{ $subscription->plan->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-sm text-gray-600 pt-1">Subscription Period</td>
                                        </tr>
                                        <tr>
                                            <td class="text-xs text-gray-500 pt-1">{{ $subscription->starts_at->format('M d, Y') }} - {{ $subscription->ends_at->format('M d, Y') }}</td>
                                        </tr>
                                        @if($subscription->is_trial)
                                            <tr>
                                                <td class="pt-2">
                                                    <span class="inline-block px-2 py-0.5 bg-purple-100 text-purple-800 text-xs rounded-full">Trial Period</span>
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-900 align-top text-sm">{{ $subscription->plan->duration_days }} days</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 align-top text-sm">${{ number_format($subscription->plan->price, 2) }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 align-top text-sm">${{ number_format($subscription->plan->price, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>

            <!-- Plan Features -->
            <tr>
                <td colspan="2" class="pt-5 pb-5">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h3 class="text-xs font-semibold text-gray-900 mb-3">Plan Features Included:</h3>
                        <table class="w-full">
                            <tr>
                                <td class="align-top w-1/2 pr-2">
                                    <table class="w-full">
                                        @foreach($subscription->plan->features->chunk(2)[0] ?? [] as $feature)
                                            <tr>
                                                <td class="py-1">
                                                    <table class="w-full">
                                                        <tr>
                                                            <td class="w-5 align-top">
                                                                <i class="fas fa-check-circle text-green-600 text-xs"></i>
                                                            </td>
                                                            <td class="text-sm text-gray-700">
                                                                <span class="font-medium">{{ ucwords(str_replace('_', ' ', $feature->feature_key)) }}:</span>
                                                                {{ $feature->feature_value }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td class="align-top w-1/2 pl-2">
                                    <table class="w-full">
                                        @foreach($subscription->plan->features->chunk(2)[1] ?? [] as $feature)
                                            <tr>
                                                <td class="py-1">
                                                    <table class="w-full">
                                                        <tr>
                                                            <td class="w-5 align-top">
                                                                <i class="fas fa-check-circle text-green-600 text-xs"></i>
                                                            </td>
                                                            <td class="text-sm text-gray-700">
                                                                <span class="font-medium">{{ ucwords(str_replace('_', ' ', $feature->feature_key)) }}:</span>
                                                                {{ $feature->feature_value }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>

            <!-- Totals -->
            <tr>
                <td class="pt-5"></td>
                <td class="pt-5" style="width: 250px;">
                    <table class="w-full">
                        <tr>
                            <td class="text-gray-600 py-2 text-sm">Subtotal:</td>
                            <td class="text-gray-900 font-medium text-right py-2 text-sm">${{ number_format($subscription->plan->price, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-gray-600 py-2 text-sm">Tax (0%):</td>
                            <td class="text-gray-900 font-medium text-right py-2 text-sm">$0.00</td>
                        </tr>
                        <tr>
                            <td class="text-gray-600 py-2 text-sm">Discount:</td>
                            <td class="text-gray-900 font-medium text-right py-2 text-sm">$0.00</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border-t py-2"></td>
                        </tr>
                        <tr>
                            <td class="text-base font-bold text-gray-900">Total:</td>
                            <td class="text-xl font-bold text-gray-900 text-right">${{ number_format($subscription->plan->price, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Spacer to push footer to bottom -->
            <tr>
                <td colspan="2" style="height: 100%;"></td>
            </tr>

            <!-- Footer Notes -->
            <tr>
                <td colspan="2" class="pt-8 border-t">
                    <table class="w-full">
                        <tr>
                            <td>
                                <h3 class="text-xs font-semibold text-gray-900 mb-2">Notes:</h3>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-sm text-gray-600 pb-3">
                                Thank you for your subscription! This invoice confirms your {{ $subscription->plan->name }} membership.
                                Your subscription is {{ $subscription->auto_renew ? 'set to auto-renew' : 'not set to auto-renew' }} on {{ $subscription->ends_at->format('M d, Y') }}.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td colspan="2" class="pt-4 border-t text-center">
                    <table class="w-full">
                        <tr>
                            <td class="text-xs text-gray-500 py-1">This is a computer-generated invoice and does not require a signature.</td>
                        </tr>
                        <tr>
                            <td class="text-xs text-gray-500 py-1">© {{ date('Y') }} Afrisellers. All rights reserved.</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
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
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 20px !important;
            background: white !important;
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            display: flex !important;
            flex-direction: column !important;
        }

        /* Ensure table takes full height */
        #invoice-content > table {
            height: 100% !important;
            display: table !important;
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

        /* Ensure proper page breaks */
        table {
            page-break-inside: avoid;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
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
