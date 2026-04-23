@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Partners</h1>
            <p class="mt-1 text-xs text-gray-500">Manage trusted partner logos displayed on the homepage</p>
        </div>
        <a href="{{ route('admin.partners.create') }}"
           class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 font-medium text-sm">
            <i class="fas fa-plus"></i> Add Partner
        </a>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600"></i>
            <p class="text-sm text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()"><i class="fas fa-times text-green-600"></i></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-handshake text-blue-600 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-semibold uppercase">Total</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-semibold uppercase">Active</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['active'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-eye-slash text-gray-500 text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-semibold uppercase">Inactive</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['inactive'] }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-gray-200 p-3">
        <form method="GET" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search partners..."
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] w-52">
            <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                <option value="">All Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg font-medium">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.partners.index') }}" class="px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg">
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Logo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Industry</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($partners as $partner)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $partner->sort_order }}</td>
                            <td class="px-4 py-3">
                                @if($partner->logo_url)
                                    <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}"
                                         class="h-10 w-auto max-w-[100px] object-contain">
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900">{{ $partner->name }}</p>
                                @if($partner->website_url)
                                    <a href="{{ $partner->website_url }}" target="_blank"
                                       class="text-xs text-blue-600 hover:underline truncate block max-w-[160px]">
                                        {{ $partner->website_url }}
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $partner->industry ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($partner->partner_type)
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-semibold rounded-full border border-blue-200">
                                        {{ $partner->partner_type }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $partner->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $partner->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.partners.show', $partner) }}"
                                       class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.partners.edit', $partner) }}"
                                       class="p-1.5 text-gray-500 hover:text-yellow-600 hover:bg-yellow-50 rounded" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.partners.toggle-status', $partner) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded" title="Toggle Status">
                                            <i class="fas fa-{{ $partner->is_active ? 'eye-slash' : 'eye' }} text-xs"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.partners.destroy', $partner) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Delete this partner?')">
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
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                <i class="fas fa-handshake text-4xl mb-3 block"></i>
                                No partners found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($partners->hasPages())
            <div class="px-4 py-3 border-t">{{ $partners->links() }}</div>
        @endif
    </div>
</div>
@endsection
