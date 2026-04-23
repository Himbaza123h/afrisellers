@extends('layouts.home')

@section('page-content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Agent Requests</h1>
            <p class="text-xs text-gray-500">Review and respond to agent applications</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600"></i>
            <p class="text-sm font-medium text-green-900">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-600"></i>
            <p class="text-sm font-medium text-red-900">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">Approved</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">Rejected</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.agent-requests.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Name, email, city..."
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#ff0808]">
                    <option value="">All</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-[#dd0606]">
                Filter
            </button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.agent-requests.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Applicant</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Location</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Company</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Submitted</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($agentRequests as $req)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $req->user->name ?? '—' }}</p>
                                <p class="text-xs text-gray-400">{{ $req->user->email ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $req->city }}, {{ $req->country->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $req->company_name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $sc = ['pending'=>'bg-yellow-100 text-yellow-800','approved'=>'bg-green-100 text-green-800','rejected'=>'bg-red-100 text-red-800'];
                                    $sc2 = $sc[$req->status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $sc2 }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $req->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.agent-requests.show', $req) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-xs font-medium">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <form method="POST" action="{{ route('admin.agent-requests.destroy', $req) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this request?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-red-300 text-red-600 rounded-lg hover:bg-red-50 text-xs font-medium">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-gray-400">
                                <i class="fas fa-user-tie text-3xl mb-2"></i>
                                <p class="text-sm">No agent requests found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($agentRequests->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $agentRequests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
