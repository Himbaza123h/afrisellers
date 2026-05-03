@extends('layouts.home')

@section('page-content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('agent.vendors.index') }}" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $vendor->businessProfile?->business_name ?? 'Vendor Profile' }}</h1>
                <p class="text-xs text-gray-500 mt-0.5">Vendor ID: #{{ $vendor->id }}</p>
            </div>
        </div>
            <div class="flex flex-wrap gap-2">
            <a href="{{ route('agent.vendors.edit', $vendor->id) }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            @if($vendor->account_status === 'active')
                <button type="button" onclick="switchToVendor({{ $vendor->id }})"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium shadow-sm">
                    <i class="fas fa-exchange-alt"></i> Switch to Vendor Dashboard
                </button>
            @endif
            @if($vendor->account_status === 'active')
                <form action="{{ route('agent.vendors.suspend', $vendor->id) }}" method="POST" class="inline"
                      onsubmit="return confirm('Suspend this vendor?')">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 text-sm font-medium shadow-sm">
                        <i class="fas fa-ban"></i> Suspend
                    </button>
                </form>
            @else
                <form action="{{ route('agent.vendors.activate', $vendor->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium shadow-sm">
                        <i class="fas fa-check"></i> Activate
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 flex-1 font-medium">{!! session('success') !!}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left: Profile Card --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Status + Avatar --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <div class="w-16 h-16 rounded-xl bg-purple-100 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-store text-purple-600 text-2xl"></i>
                </div>
                <h2 class="text-base font-bold text-gray-900">{{ $vendor->businessProfile?->business_name }}</h2>
                <p class="text-sm text-gray-500 mb-3">{{ $vendor->businessProfile?->business_type ?? 'Business' }}</p>
                @php
                    $statusMap = [
                        'active'    => ['bg-green-100 text-green-700',   'Active'],
                        'pending'   => ['bg-yellow-100 text-yellow-700', 'Pending'],
                        'suspended' => ['bg-red-100 text-red-700',       'Suspended'],
                        'rejected'  => ['bg-gray-100 text-gray-600',     'Rejected'],
                    ];
                    [$cls, $label] = $statusMap[$vendor->account_status] ?? ['bg-gray-100 text-gray-600', 'Unknown'];
                @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                    <i class="fas fa-circle text-[6px]"></i> {{ $label }}
                </span>
            </div>

            {{-- Contact Details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Contact Details</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-blue-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Contact Person</p>
                            <p class="text-sm font-medium text-gray-800">{{ $vendor->user?->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-green-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Email</p>
                            <p class="text-sm font-medium text-gray-800">{{ $vendor->user?->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-purple-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Phone</p>
                            <p class="text-sm font-medium text-gray-800">{{ $vendor->businessProfile?->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-orange-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Location</p>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $vendor->businessProfile?->city ? $vendor->businessProfile->city . ', ' : '' }}
                                {{ $vendor->businessProfile?->country?->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    @if($vendor->businessProfile?->website)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-sky-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-globe text-sky-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Website</p>
                                <a href="{{ $vendor->businessProfile->website }}" target="_blank"
                                   class="text-sm font-medium text-blue-600 hover:underline truncate block max-w-[160px]">
                                    {{ $vendor->businessProfile->website }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('agent.vendors.edit', $vendor->id) }}"
                       class="flex items-center gap-2 w-full px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-edit text-green-500 w-4"></i> Edit Vendor Info
                    </a>
                    <a href="{{ route('agent.vendors.commissions', $vendor->id) }}"
                       class="flex items-center gap-2 w-full px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-percentage text-blue-500 w-4"></i> View Commissions
                    </a>
                    <a href="{{ route('agent.vendors.orders', $vendor->id) }}"
                       class="flex items-center gap-2 w-full px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-shopping-bag text-purple-500 w-4"></i> View Orders
                    </a>
                    <a href="{{ route('agent.vendors.products.index', $vendor->id) }}"
                    class="flex items-center gap-2 w-full px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-box text-orange-500 w-4"></i> Manage Products
                    </a>
                    <hr class="border-gray-100 my-1">
                    <form action="{{ route('agent.vendors.destroy', $vendor->id) }}" method="POST"
                          onsubmit="return confirm('Remove this vendor from your account? The vendor account itself will not be deleted.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                            <i class="fas fa-unlink w-4"></i> Remove from My Account
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Details --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Business Details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Business Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach([
                        ['Business Name',    $vendor->businessProfile?->business_name],
                        ['Business Type',    $vendor->businessProfile?->business_type],
                        ['Country',          $vendor->businessProfile?->country?->name],
                        ['City',             $vendor->businessProfile?->city],
                        ['Address',          $vendor->businessProfile?->address],
                        ['Account Status',   ucfirst($vendor->account_status)],
                        ['Email Verified',   $vendor->email_verified ? 'Yes' : 'No'],
                        ['Onboarded',        $vendor->created_at->format('M d, Y')],
                    ] as [$label, $value])
                        <div>
                            <dt class="text-xs text-gray-400 font-medium mb-0.5">{{ $label }}</dt>
                            <dd class="text-sm text-gray-800 font-medium">{{ $value ?? '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
                @if($vendor->businessProfile?->description)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <dt class="text-xs text-gray-400 font-medium mb-1">Description</dt>
                        <dd class="text-sm text-gray-700 leading-relaxed">{{ $vendor->businessProfile->description }}</dd>
                    </div>
                @endif
            </div>

            {{-- Meta --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">Account Meta</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Vendor ID</dt>
                        <dd class="text-sm font-mono font-bold text-gray-800">#{{ $vendor->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Created</dt>
                        <dd class="text-sm font-medium text-gray-800">{{ $vendor->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Last Updated</dt>
                        <dd class="text-sm font-medium text-gray-800">{{ $vendor->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Recent Products --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Recent Products</h3>
                    <a href="{{ route('agent.vendors.products.index', $vendor->id) }}"
                    class="text-xs text-blue-600 hover:underline font-medium">
                        View All →
                    </a>
                </div>

                @if($recentProducts->isEmpty())
                    <div class="text-center py-6">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-box text-gray-300 text-xl"></i>
                        </div>
                        <p class="text-sm text-gray-500">No products yet</p>
                        <a href="{{ route('agent.vendors.products.create', $vendor->id) }}"
                        class="mt-2 inline-flex items-center gap-1 text-xs text-blue-600 hover:underline">
                            <i class="fas fa-plus"></i> Add first product
                        </a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentProducts as $product)
                            @php
                                $thumb = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                                $statusColors = [
                                    'active'   => 'bg-green-100 text-green-700',
                                    'inactive' => 'bg-red-100 text-red-700',
                                    'draft'    => 'bg-gray-100 text-gray-600',
                                ];
                                $sc = $statusColors[$product->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    @if($thumb)
                                        <img src="{{ $thumb->image_url }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <i class="fas fa-image text-gray-300 text-lg"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $product->productCategory?->name ?? '—' }}</p>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                    <a href="{{ route('agent.vendors.products.edit', [$vendor->id, $product->id]) }}"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <a href="{{ route('agent.vendors.products.create', $vendor->id) }}"
                        class="flex items-center justify-center gap-2 w-full px-3 py-2 bg-orange-50 text-orange-600 rounded-lg text-sm font-medium hover:bg-orange-100 transition-colors">
                            <i class="fas fa-plus"></i> Add New Product
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@push('scripts')
<script>
function switchToVendor(vendorId) {
    if (!confirm('Switch to this vendor\'s dashboard?')) return;

    fetch(`/agent/vendors/${vendorId}/switch`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.open(data.login_url, '_blank');
        } else {
            alert(data.message || 'Failed to switch.');
        }
    })
    .catch(() => alert('Something went wrong.'));
}
</script>
@endpush
@endsection
