@extends('layouts.home')
@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Partners</h1>
            <p class="mt-1 text-xs text-gray-500">Partners you have registered</p>
        </div>
        <a href="{{ route('agent.partners.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold shadow-sm">
            <i class="fas fa-plus"></i> Register Partner
        </a>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800 flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Company</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Registered</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($partners as $pr)
                        @php
                            $isApproved = \App\Models\Partner::where('partner_request_id', $pr->id)->exists();
                            $statusMap  = [
                                'pending'  => 'bg-amber-100 text-amber-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-xs text-gray-400 font-mono">
                                {{ str_pad($pr->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($pr->logo)
                                        <img src="{{ Storage::url($pr->logo) }}"
                                            class="w-8 h-8 rounded-lg object-contain border border-gray-200 bg-gray-50">
                                    @else
                                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-handshake text-teal-600 text-xs"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $pr->company_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $pr->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-800">{{ $pr->contact_name }}</p>
                                <p class="text-xs text-gray-400">{{ $pr->phone }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-600">{{ $pr->partner_type ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusMap[$pr->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($pr->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $pr->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('agent.partners.show', $pr->id) }}"
                                        class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    @if($isApproved)
                                        <button onclick="agentSwitchToPartner({{ $pr->id }})"
                                            class="px-3 py-1.5 text-xs font-semibold text-teal-600 bg-teal-50 rounded-lg hover:bg-teal-100 transition-all">
                                            <i class="fas fa-exchange-alt mr-1"></i> Switch
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-handshake text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 mb-1">No partners registered yet</p>
                                    <p class="text-xs text-gray-500 mb-4">Register your first partner to get started</p>
                                    <a href="{{ route('agent.partners.create') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700">
                                        <i class="fas fa-plus"></i> Register Partner
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($partners->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $partners->links() }}
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
