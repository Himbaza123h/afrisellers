@extends('layouts.home')
@section('page-content')
<div class="max-w-6xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('agent.partners.index') }}"
            class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $partnerRequest->company_name }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">Partner details</p>
        </div>
    </div>

    {{-- Status banner --}}
    @php
        $statusMap = [
            'pending'  => ['bg-amber-50 border-amber-200', 'text-amber-700', 'fa-hourglass-half'],
            'approved' => ['bg-green-50 border-green-200',  'text-green-700', 'fa-check-circle'],
            'rejected' => ['bg-red-50 border-red-200',      'text-red-700',   'fa-times-circle'],
        ];
        [$bannerClass, $textClass, $icon] = $statusMap[$partnerRequest->status] ?? ['bg-gray-50 border-gray-200','text-gray-700','fa-circle'];
    @endphp
    <div class="p-4 rounded-xl border {{ $bannerClass }} flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fas {{ $icon }} {{ $textClass }} text-lg"></i>
            <div>
                <p class="text-sm font-bold {{ $textClass }}">{{ ucfirst($partnerRequest->status) }}</p>
                @if($partnerRequest->status === 'rejected' && $partnerRequest->rejection_reason)
                    <p class="text-xs text-red-600 mt-0.5">{{ $partnerRequest->rejection_reason }}</p>
                @endif
            </div>
        </div>
        @if($partner)
            <button onclick="agentSwitchToPartner({{ $partnerRequest->id }})"
                class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 text-sm font-semibold transition-all">
                <i class="fas fa-exchange-alt"></i> Switch to Dashboard
            </button>
        @endif
    </div>

    {{-- Info card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">

        <div class="flex items-center gap-4">
            @if($partnerRequest->logo)
                <img src="{{ Storage::url($partnerRequest->logo) }}"
                    class="w-16 h-16 rounded-xl object-contain border border-gray-200 bg-gray-50">
            @else
                <div class="w-16 h-16 bg-teal-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-handshake text-teal-600 text-2xl"></i>
                </div>
            @endif
            <div>
                <p class="text-lg font-bold text-gray-900">{{ $partnerRequest->company_name }}</p>
                <p class="text-sm text-gray-500">{{ $partnerRequest->partner_type ?? 'Partner' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-gray-100">
            @foreach([
                ['Contact',   $partnerRequest->contact_name, 'fa-user'],
                ['Email',     $partnerRequest->email,         'fa-envelope'],
                ['Phone',     $partnerRequest->phone ?? '—',  'fa-phone'],
                ['Industry',  $partnerRequest->industry ?? '—','fa-industry'],
                ['Country',   $partnerRequest->country ?? '—', 'fa-globe'],
                ['Website',   $partnerRequest->website_url ?? '—','fa-link'],
                ['Registered', $partnerRequest->created_at->format('M d, Y'), 'fa-calendar'],
            ] as [$label, $value, $ico])
                <div class="flex items-start gap-2">
                    <i class="fas {{ $ico }} text-gray-400 text-xs mt-1 w-4 flex-shrink-0"></i>
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase">{{ $label }}</p>
                        <p class="text-sm text-gray-800 break-all">{{ $value }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        @if($partnerRequest->message)
            <div class="pt-3 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Description</p>
                <p class="text-sm text-gray-700">{{ $partnerRequest->message }}</p>
            </div>
        @endif
    </div>
</div>

<script>
function agentSwitchToPartner(id) {
    fetch(`/agent/partners/${id}/switch`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.login_url;
        } else {
            alert(data.message);
        }
    });
}
</script>
@endsection
