@extends('layouts.home')

@section('page-content')
<div class="space-y-4 max-w-5xl">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex gap-3 items-center">
            <a href="{{ route('admin.memberships.plans.index') }}" class="p-2 rounded-lg transition-colors hover:bg-gray-100">
                <i class="text-gray-600 fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Feature catalog</h1>
                <p class="mt-0.5 text-xs text-gray-500">Definitions (keys) used across membership plans — click <span class="font-medium">Status</span> or <span class="font-medium">Supported</span> to toggle without opening edit.</p>
            </div>
        </div>
        {{-- <a href="{{ route('admin.memberships.feature-catalog.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] hover:bg-[#e60707] text-white rounded-lg text-sm font-medium">
            <i class="fas fa-plus"></i> New feature
        </a> --}}
    </div>

    <div id="catalogToggleFlash" class="hidden p-3 text-sm rounded-lg border"></div>

    @if(session('success'))
        <div class="p-3 text-sm text-green-900 bg-green-50 rounded-lg border border-green-200">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-3 text-sm text-red-900 bg-red-50 rounded-lg border border-red-200">{{ session('error') }}</div>
    @endif

    <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
        <form method="get" action="{{ route('admin.memberships.feature-catalog.index') }}" class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="catalog-search" class="block mb-1 text-xs font-medium text-gray-600">Search</label>
                <div class="relative">
                    <i class="absolute left-3 top-1/2 text-gray-400 -translate-y-1/2 pointer-events-none fas fa-search text-xs"></i>
                    <input type="text" id="catalog-search" name="search" value="{{ request('search') }}" placeholder="Name, key, slug, description…"
                           class="py-2 pr-3 pl-9 w-full text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-200">
                </div>
            </div>
            <div class="w-full sm:w-40">
                <label for="catalog-status" class="block mb-1 text-xs font-medium text-gray-600">Status</label>
                <select id="catalog-status" name="status" class="py-2 px-3 w-full text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-200">
                    <option value="" @selected(request('status') === null || request('status') === '')>All</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="w-full sm:w-40">
                <label for="catalog-supported" class="block mb-1 text-xs font-medium text-gray-600">Supported</label>
                <select id="catalog-supported" name="supported" class="py-2 px-3 w-full text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-200">
                    <option value="" @selected(request('supported', '') === '' || request('supported') === null)>All</option>
                    <option value="1" @selected(request('supported') === '1')>Yes</option>
                    <option value="0" @selected(request('supported') === '0')>No</option>
                </select>
            </div>
            <div class="w-full sm:w-44">
                <label for="catalog-value-type" class="block mb-1 text-xs font-medium text-gray-600">Value type</label>
                <select id="catalog-value-type" name="value_type" class="py-2 px-3 w-full text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-200">
                    <option value="" @selected(request('value_type') === null || request('value_type') === '')>All</option>
                    @foreach(\App\Models\Feature::VALUE_TYPES as $vt)
                        <option value="{{ $vt }}" @selected(request('value_type') === $vt)>{{ str_replace('_', ' ', $vt) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#ff0808] rounded-lg border border-transparent hover:bg-[#e60707] transition-colors">
                    <i class="fas fa-filter text-xs"></i> Apply
                </button>
                <a href="{{ route('admin.memberships.feature-catalog.index') }}" class="inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg border border-gray-200 hover:bg-gray-200 transition-colors">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Description</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Value type</th>
                        {{-- <th class="px-4 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Slug</th> --}}
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Key</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold text-center text-gray-600 uppercase">Supported</th>
                        <th class="px-4 py-3 text-xs font-semibold text-right text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($features as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $row->name }}</td>
                            <td class="px-4 py-3 max-w-xs text-xs leading-relaxed text-gray-600">{{ $row->description ? \Illuminate\Support\Str::limit($row->description, 120) : '—' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700"><code class="px-1.5 py-0.5 bg-gray-100 rounded">{{ str_replace('_', ' ', $row->resolvedValueType()) }}</code></td>
                            <td class="px-4 py-3"><code class="px-2 py-0.5 text-xs text-blue-700 bg-blue-50 rounded">{{ $row->feature_key }}</code></td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-catalog-toggle-status
                                        data-url="{{ route('admin.memberships.feature-catalog.toggle-status', $row) }}"
                                        data-status="{{ $row->status }}"
                                        title="Click to toggle active / inactive"
                                        @class([
                                            'catalog-flag-btn inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1',
                                            'bg-green-100 text-green-800 border-green-200 hover:bg-green-200 focus:ring-green-400' => $row->status === 'active',
                                            'bg-gray-100 text-gray-700 border-gray-200 hover:bg-gray-200 focus:ring-gray-400' => $row->status !== 'active',
                                        ])>
                                    {{ $row->status === 'active' ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                        data-catalog-toggle-supported
                                        data-url="{{ route('admin.memberships.feature-catalog.toggle-supported', $row) }}"
                                        data-supported="{{ $row->is_supported ? '1' : '0' }}"
                                        title="Click to toggle supported for new plan assignments"
                                        @class([
                                            'catalog-flag-btn inline-flex items-center justify-center min-w-[4.5rem] px-2.5 py-1 rounded-full text-xs font-medium border transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1',
                                            'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100 focus:ring-emerald-400' => $row->is_supported,
                                            'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200 focus:ring-gray-400' => ! $row->is_supported,
                                        ])>
                                    {{ $row->is_supported ? 'Yes' : 'No' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 space-x-1 text-right">
                                <a href="{{ route('admin.memberships.feature-catalog.edit', $row) }}" class="inline-flex p-2 text-blue-600 rounded-lg hover:bg-blue-50"><i class="fas fa-edit"></i></a>
                                {{-- <form action="{{ route('admin.memberships.feature-catalog.destroy', $row) }}" method="POST" class="inline" onsubmit="return confirm('Delete this feature definition?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex p-2 text-red-600 rounded-lg hover:bg-red-50"><i class="fas fa-trash"></i></button>
                                </form> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                @if($hasFilters ?? false)
                                    No features match your search or filters. <a href="{{ route('admin.memberships.feature-catalog.index') }}" class="font-medium text-purple-700 underline hover:text-purple-900">Clear filters</a>
                                @else
                                    No catalog features yet. Create one or run migrations after seeding plans.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($features->hasPages())
            <div class="px-4 py-3 bg-gray-50 border-t">{{ $features->links() }}</div>
        @endif
    </div>
</div>

<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function showFlash(kind, text) {
        const el = document.getElementById('catalogToggleFlash');
        if (!el || !text) return;
        el.classList.remove('hidden', 'text-green-900', 'bg-green-50', 'border-green-200', 'text-red-900', 'bg-red-50', 'border-red-200');
        if (kind === 'error') {
            el.classList.add('text-red-900', 'bg-red-50', 'border-red-200');
        } else {
            el.classList.add('text-green-900', 'bg-green-50', 'border-green-200');
        }
        el.textContent = text;
        el.classList.remove('hidden');
        clearTimeout(showFlash._t);
        showFlash._t = setTimeout(() => { el.classList.add('hidden'); el.textContent = ''; }, 3500);
    }

    function statusClasses(st) {
        const active = st === 'active';
        return active
            ? 'catalog-flag-btn inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 bg-green-100 text-green-800 border-green-200 hover:bg-green-200 focus:ring-green-400'
            : 'catalog-flag-btn inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 bg-gray-100 text-gray-700 border-gray-200 hover:bg-gray-200 focus:ring-gray-400';
    }

    function supportedClasses(yes) {
        return yes
            ? 'catalog-flag-btn inline-flex items-center justify-center min-w-[4.5rem] px-2.5 py-1 rounded-full text-xs font-medium border transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100 focus:ring-emerald-400'
            : 'catalog-flag-btn inline-flex items-center justify-center min-w-[4.5rem] px-2.5 py-1 rounded-full text-xs font-medium border transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200 focus:ring-gray-400';
    }

    document.addEventListener('click', async function (e) {
        const statusBtn = e.target.closest('[data-catalog-toggle-status]');
        const supBtn = e.target.closest('[data-catalog-toggle-supported]');
        const btn = statusBtn || supBtn;
        if (!btn) return;

        const url = btn.getAttribute('data-url');
        if (!url || btn.disabled) return;

        btn.disabled = true;
        const res = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({}),
        });
        let data = {};
        try { data = await res.json(); } catch (_) {}
        btn.disabled = false;

        if (!res.ok) {
            showFlash('error', data.message || 'Could not update. Try again.');
            return;
        }

        if (statusBtn && data.status) {
            statusBtn.setAttribute('data-status', data.status);
            statusBtn.textContent = data.status === 'active' ? 'Active' : 'Inactive';
            statusBtn.className = statusClasses(data.status);
            showFlash('ok', data.message || 'Status updated.');
            return;
        }

        if (supBtn && typeof data.is_supported === 'boolean') {
            const yes = data.is_supported;
            supBtn.setAttribute('data-supported', yes ? '1' : '0');
            supBtn.textContent = yes ? 'Yes' : 'No';
            supBtn.className = supportedClasses(yes);
            showFlash('ok', data.message || 'Supported updated.');
        }
    });
})();
</script>
@endsection
