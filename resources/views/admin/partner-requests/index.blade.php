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
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <form method="GET" class="space-y-3">

            {{-- Row 1: Search + Status + Type --}}
            <div class="flex flex-wrap gap-2 items-center">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Name, email, phone..."
                           class="pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] w-56">
                </div>

                <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                    <option value="">All Statuses</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>

                <select name="partner_type" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                    <option value="">All Types</option>
                    @foreach($partnerTypes as $type)
                        <option value="{{ $type }}" {{ request('partner_type') === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>

                <select name="country" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>
                            {{ $country }}
                        </option>
                    @endforeach
                </select>

                <input type="text" name="industry" value="{{ request('industry') }}"
                       placeholder="Industry..."
                       class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] w-36">
            </div>

            {{-- Row 2: Date range + buttons --}}
            <div class="flex flex-wrap gap-2 items-center">
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-500 font-medium whitespace-nowrap">Date from</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-500 font-medium whitespace-nowrap">to</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                </div>

                <button type="submit"
                        class="px-4 py-2 bg-[#ff0808] text-white text-sm rounded-lg font-medium hover:bg-red-700 transition-all flex items-center gap-1.5">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>

                @if(request()->hasAny(['search','status','partner_type','country','industry','date_from','date_to']))
                    <a href="{{ route('admin.partner-requests.index') }}"
                       class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-all flex items-center gap-1.5">
                        <i class="fas fa-times text-xs"></i> Clear
                    </a>
                    <span class="text-xs text-gray-400">
                        {{ $requests->total() }} result{{ $requests->total() !== 1 ? 's' : '' }} found
                    </span>
                @endif
            </div>
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Country</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Est.</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Countries</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Submitted</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requests as $req)
                        <tr class="hover:bg-gray-50">
                            {{-- Company --}}
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
                                        @if($req->intro_url)
                                            <span class="text-[10px] text-purple-500 font-medium">
                                                <i class="fas fa-photo-video mr-0.5"></i> Has intro
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td class="px-4 py-3">
                                <p class="text-xs font-medium text-gray-900">{{ $req->contact_name }}</p>
                                <p class="text-[10px] text-gray-500">{{ $req->email }}</p>
                                @if($req->phone)
                                    <p class="text-[10px] text-gray-400">{{ $req->phone }}</p>
                                @endif
                            </td>

                            {{-- Industry --}}
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $req->industry ?? '—' }}</td>

                            {{-- Type --}}
                            <td class="px-4 py-3">
                                @if($req->partner_type)
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-semibold rounded-full">
                                        {{ $req->partner_type }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Country --}}
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $req->country ?? '—' }}</td>

                            {{-- Established --}}
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $req->established ?? '—' }}</td>

                            {{-- Presence countries --}}
                            <td class="px-4 py-3">
                                @if($req->presence_countries)
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-700 font-medium">
                                        <i class="fas fa-globe text-gray-400 text-[10px]"></i>
                                        {{ $req->presence_countries }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Submitted --}}
                            <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                {{ $req->created_at->format('M d, Y') }}
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3">
                                @php
                                    $colors = ['pending' => 'amber', 'approved' => 'green', 'rejected' => 'red'];
                                    $c = $colors[$req->status] ?? 'gray';
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-{{ $c }}-100 text-{{ $c }}-700 capitalize">
                                    {{ $req->status }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.partner-requests.show', $req) }}"
                                       class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.partner-requests.destroy', $req) }}" method="POST"
                                          class="inline" onsubmit="return confirm('Delete this request?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded" title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-gray-400">
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
