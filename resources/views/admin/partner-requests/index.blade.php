@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Partner Requests</h1>
            <p class="mt-1 text-xs text-gray-500">Review and manage incoming partnership applications</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600"></i>
            <p class="text-sm text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()"><i class="fas fa-times text-green-600"></i></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
            ['label' => 'Total',    'value' => $stats['total'],    'color' => 'blue',   'icon' => 'inbox'],
            ['label' => 'Pending',  'value' => $stats['pending'],  'color' => 'amber',  'icon' => 'clock'],
            ['label' => 'Approved', 'value' => $stats['approved'], 'color' => 'green',  'icon' => 'check-circle'],
            ['label' => 'Rejected', 'value' => $stats['rejected'], 'color' => 'red',    'icon' => 'times-circle'],
        ] as $stat)
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-{{ $stat['color'] }}-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-{{ $stat['icon'] }} text-{{ $stat['color'] }}-600 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-semibold uppercase">{{ $stat['label'] }}</p>
                <p class="text-lg font-bold text-gray-900">{{ $stat['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-gray-200 p-3">
        <form method="GET" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name, email..."
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] w-52">
            <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                <option value="">All Status</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button type="submit" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg font-medium">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.partner-requests.index') }}" class="px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Company</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Industry</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Submitted</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requests as $req)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($req->logo_url)
                                        <img src="{{ $req->logo_url }}" alt="{{ $req->company_name }}"
                                             class="h-8 w-auto max-w-[60px] object-contain">
                                    @else
                                        <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center">
                                            <i class="fas fa-building text-gray-400 text-xs"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-900 text-xs">{{ $req->company_name }}</p>
                                        @if($req->website_url)
                                            <a href="{{ $req->website_url }}" target="_blank"
                                               class="text-[10px] text-blue-600 hover:underline truncate block max-w-[120px]">
                                                {{ $req->website_url }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs font-medium text-gray-900">{{ $req->contact_name }}</p>
                                <p class="text-[10px] text-gray-500">{{ $req->email }}</p>
                                @if($req->phone)
                                    <p class="text-[10px] text-gray-400">{{ $req->phone }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $req->industry ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($req->partner_type)
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-semibold rounded-full">
                                        {{ $req->partner_type }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $req->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $colors = ['pending' => 'amber', 'approved' => 'green', 'rejected' => 'red'];
                                    $c = $colors[$req->status] ?? 'gray';
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-{{ $c }}-100 text-{{ $c }}-700 capitalize">
                                    {{ $req->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.partner-requests.show', $req) }}"
                                       class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.partner-requests.destroy', $req) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Delete this request?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded" title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3 block"></i>
                                No partner requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="px-4 py-3 border-t">{{ $requests->links() }}</div>
        @endif
    </div>
</div>
@endsection
