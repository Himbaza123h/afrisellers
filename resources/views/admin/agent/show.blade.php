@extends('layouts.home')

@section('page-content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.agents.index') }}" class="p-2 text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $agent->user->name ?? 'Agent' }}</h1>
                <p class="text-xs text-gray-500">{{ $agent->user->email ?? '' }} &bull; {{ $agent->company_name ?? 'No company' }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.agents.edit', $agent) }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('admin.agents.destroy', $agent) }}" method="POST"
                  onsubmit="return confirm('Delete this agent and unlink all vendors?')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-red-50 border border-red-200 text-red-700 rounded-lg hover:bg-red-100 text-sm font-medium shadow-sm">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if($errors->any())
        <div class="p-3 bg-red-50 rounded-lg border border-red-200">
            @foreach($errors->all() as $e)
                <p class="text-sm text-red-700">• {{ $e }}</p>
            @endforeach
        </div>
    @endif

    {{-- Top stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 mb-1">Commission Earned</p>
            <p class="text-2xl font-bold text-gray-900">${{ number_format($agent->commission_earned, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Rate: {{ $agent->commission_rate }}%</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 mb-1">Total Sales</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($agent->total_sales) }}</p>
            <p class="text-xs text-gray-400 mt-1">All time</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 mb-1">Vendors</p>
            <p class="text-2xl font-bold text-gray-900">{{ $vendors->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">Linked to this agent</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 mb-1">Total Products</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalProducts }}</p>
            <p class="text-xs text-gray-400 mt-1">Across all vendors</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: agent info + actions --}}
        <div class="space-y-5">

            {{-- Info card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
                <h3 class="text-sm font-semibold text-gray-900 border-b pb-2">Agent Details</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="font-medium">{{ $agent->phone_code }} {{ $agent->phone }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Location</dt>
                        <dd class="font-medium">{{ $agent->city }}, {{ $agent->country->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            @php
                                $sc = ['active'=>'bg-green-100 text-green-800','pending'=>'bg-yellow-100 text-yellow-800','suspended'=>'bg-red-100 text-red-800'];
                                $c  = $sc[$agent->account_status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $c }}">
                                {{ ucfirst($agent->account_status) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Email</dt>
                        <dd>
                            @if($agent->email_verified)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    <i class="fas fa-check-circle mr-1"></i>Verified
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Unverified</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Joined</dt>
                        <dd class="font-medium">{{ $agent->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>

                <div class="pt-2 space-y-2 border-t">
                    @if(in_array($agent->account_status, ['pending', 'suspended']))
                        <form action="{{ route('admin.agents.activate', $agent) }}" method="POST">
                            @csrf
                            <button class="w-full py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                                <i class="fas fa-check mr-1"></i> Activate
                            </button>
                        </form>
                    @endif
                    @if($agent->account_status === 'active')
                        <form action="{{ route('admin.agents.suspend', $agent) }}" method="POST"
                              onsubmit="return confirm('Suspend this agent?')">
                            @csrf
                            <button class="w-full py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600">
                                <i class="fas fa-ban mr-1"></i> Suspend
                            </button>
                        </form>
                    @endif
                    @if(!$agent->email_verified)
                        <form action="{{ route('admin.agents.verify-email', $agent) }}" method="POST">
                            @csrf
                            <button class="w-full py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700">
                                <i class="fas fa-envelope mr-1"></i> Verify Email
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Assign vendor form --}}
            @if($availableVendors->count())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 border-b pb-2 mb-4">
                    <i class="fas fa-link mr-1 text-blue-500"></i> Assign a Vendor
                </h3>
                <form action="{{ route('admin.agents.assign-vendor', $agent) }}" method="POST" class="space-y-3">
                    @csrf
                <div class="relative">
                    <input type="text" id="vendor-search" placeholder="Search vendors..."
                        autocomplete="off"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                        oninput="filterVendors(this.value)"
                        onfocus="document.getElementById('vendor-dropdown').classList.remove('hidden')"
                    >
                    <div id="vendor-dropdown"
                        class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        @foreach($availableVendors as $v)
                            <div class="vendor-option px-3 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-[#ff0808] cursor-pointer"
                                data-value="{{ $v->id }}"
                                data-label="{{ ($v->businessProfile->business_name ?? $v->user->name ?? 'Unnamed') }} — {{ $v->businessProfile->city ?? '' }}"
                                data-search="{{ strtolower($v->businessProfile->business_name ?? $v->user->name ?? '') }} {{ strtolower($v->businessProfile->city ?? '') }}"
                                onclick="selectVendor(this)">
                                {{ $v->businessProfile->business_name ?? $v->user->name ?? 'Unnamed' }}
                                — {{ $v->businessProfile->city ?? '' }}
                            </div>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="vendor_id" id="vendor-id-input" required>
                    <button type="submit"
                        class="w-full py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-[#dd0606]">
                        <i class="fas fa-plus mr-1"></i> Assign Vendor
                    </button>
                </form>
            </div>
            @else
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 text-center text-sm text-gray-500">
                    No unassigned vendors available.
                </div>
            @endif

        </div>

        {{-- Right: vendors + their products --}}
        <div class="lg:col-span-2 space-y-4">

            <h3 class="text-sm font-semibold text-gray-900">
                Vendors ({{ $vendors->count() }})
            </h3>

            @forelse($vendors as $vendor)
                @php
                    $bp       = $vendor->businessProfile;
                    $products = $bp ? $bp->products()->latest()->take(5)->get() : collect();
                @endphp

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                    {{-- Vendor header --}}
                    <div class="flex items-start justify-between p-4 border-b border-gray-100">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ $bp->business_name ?? 'Unnamed Business' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $bp->city ?? '' }}{{ ($bp->city && $bp->country) ? ', ' : '' }}{{ $bp->country->name ?? '' }}
                                &bull; {{ $bp->phone_code ?? '' }} {{ $bp->phone ?? '' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $vendor->account_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($vendor->account_status) }}
                            </span>
                            {{-- Remove vendor --}}
                            <form action="{{ route('admin.agents.remove-vendor', [$agent, $vendor]) }}"
                                  method="POST"
                                  onsubmit="return confirm('Remove this vendor from the agent?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg"
                                    title="Remove from agent">
                                    <i class="fas fa-unlink text-sm"></i>
                                </button>
                            </form>
                            @if($bp)
                                <a href="{{ route('admin.business-profile.show', $bp) }}"
                                   class="p-1.5 text-blue-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg"
                                   title="View business profile">
                                    <i class="fas fa-external-link-alt text-sm"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Vendor stats row --}}
                    <div class="grid grid-cols-3 divide-x divide-gray-100 bg-gray-50 text-center text-xs py-2">
                        <div class="py-1">
                            <p class="font-bold text-gray-900">{{ $bp ? $bp->products()->count() : 0 }}</p>
                            <p class="text-gray-500">Products</p>
                        </div>
                        <div class="py-1">
                            <p class="font-bold text-gray-900">
                                {{ ucfirst($bp?->verification_status ?? '—') }}
                            </p>
                            <p class="text-gray-500">Verification</p>
                        </div>
                        <div class="py-1">
                            <p class="font-bold text-gray-900">{{ $vendor->created_at->format('M Y') }}</p>
                            <p class="text-gray-500">Joined</p>
                        </div>
                    </div>

                    {{-- Products list --}}
                    @if($products->count())
                        <div class="p-4">
                            <p class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">
                                Recent Products
                            </p>
                            <div class="space-y-2">
                                @foreach($products as $product)
                                    <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate">
                                                {{ $product->name ?? $product->title ?? 'Unnamed' }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ $product->created_at->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2 ml-3 shrink-0">
                                            @if(isset($product->price))
                                                <span class="text-sm font-semibold text-gray-900">
                                                    ${{ number_format($product->price, 2) }}
                                                </span>
                                            @endif
                                            @php
                                                $ps = ['active'=>'bg-green-100 text-green-700','pending'=>'bg-yellow-100 text-yellow-700','rejected'=>'bg-red-100 text-red-700'];
                                                $pc = $ps[$product->status ?? ''] ?? 'bg-gray-100 text-gray-600';
                                            @endphp
                                            <span class="px-1.5 py-0.5 rounded text-xs font-medium {{ $pc }}">
                                                {{ ucfirst($product->status ?? 'unknown') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($bp && $bp->products()->count() > 5)
                                <p class="text-xs text-gray-400 text-right mt-2">
                                    + {{ $bp->products()->count() - 5 }} more products
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="p-4 text-center text-xs text-gray-400">
                            No products yet for this vendor.
                        </div>
                    @endif

                </div>
            @empty
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm py-16 text-center">
                    <i class="fas fa-store text-4xl text-gray-200 mb-3"></i>
                    <p class="text-sm font-medium text-gray-500">No vendors assigned yet.</p>
                    <p class="text-xs text-gray-400 mt-1">Use the panel on the left to assign vendors.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function filterVendors(query) {
    const q = query.toLowerCase();
    document.getElementById('vendor-dropdown').classList.remove('hidden');
    document.querySelectorAll('.vendor-option').forEach(opt => {
        opt.style.display = opt.dataset.search.includes(q) ? '' : 'none';
    });
}

function selectVendor(el) {
    document.getElementById('vendor-search').value = el.dataset.label;
    document.getElementById('vendor-id-input').value = el.dataset.value;
    document.getElementById('vendor-dropdown').classList.add('hidden');
}

document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('vendor-dropdown');
    if (wrapper && !wrapper.contains(e.target) && e.target.id !== 'vendor-search') {
        wrapper.classList.add('hidden');
    }
});
</script>
@endpush
