@extends('layouts.home')
@section('page-content')
<div class="max-w-6xl mx-auto space-y-6">

    <div>
        <h1 class="text-xl font-bold text-gray-900">Partner Account</h1>
        <p class="text-xs text-gray-500 mt-1">Request to join Afrisellers as an official partner</p>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800 flex items-center gap-2">
            <i class="fas fa-check-circle"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800 flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>{{ session('error') }}
        </div>
    @endif

    @if($partner)
        {{-- ── APPROVED & ACTIVE ─────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-green-200 shadow-sm p-6 text-center space-y-4">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                <i class="fas fa-handshake text-green-600 text-2xl"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Your Partner Account is Active!</h2>
            <p class="text-sm text-gray-500">Switch to your partner dashboard at any time using the button below.</p>
            <button onclick="switchToPartner()"
                class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold text-sm transition-all">
                <i class="fas fa-exchange-alt"></i> Switch to Partner Dashboard
            </button>
        </div>

    @elseif($partnerRequest && $partnerRequest->status === 'rejected')
        {{-- ── REJECTED — show reason + allow edit ──────────────── --}}
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
            <i class="fas fa-times-circle text-red-500 mt-0.5 text-lg"></i>
            <div>
                <p class="text-sm font-bold text-red-800">Your request was not approved</p>
                @if($partnerRequest->rejection_reason)
                    <p class="text-sm text-red-700 mt-1">{{ $partnerRequest->rejection_reason }}</p>
                @else
                    <p class="text-sm text-red-600 mt-1">Please contact support for more details.</p>
                @endif
                <p class="text-xs text-red-500 mt-1">You can update your details below and resubmit.</p>
            </div>
        </div>

        @include('vendor.partner-request._form', ['partnerRequest' => $partnerRequest, 'isEdit' => true])

    @elseif($partnerRequest && $partnerRequest->status === 'pending')
        {{-- ── PENDING — show info + allow edit ─────────────────── --}}
        <div class="bg-white rounded-xl border border-amber-200 shadow-sm p-5 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-900">Request Submitted</h2>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Pending Review</span>
            </div>
            <p class="text-sm text-gray-600">Company: <strong>{{ $partnerRequest->company_name }}</strong></p>
            <p class="text-sm text-gray-600">Submitted: {{ $partnerRequest->created_at->format('M d, Y') }}</p>
            <p class="text-sm text-gray-500">Our team reviews requests within 2-3 business days. You can still update your details below.</p>
        </div>

        @include('vendor.partner-request._form', ['partnerRequest' => $partnerRequest, 'isEdit' => true])

    @else
        {{-- ── NEW REQUEST FORM ──────────────────────────────────── --}}
        @include('vendor.partner-request._form', ['partnerRequest' => null, 'isEdit' => false])

    @endif
</div>
@endsection
