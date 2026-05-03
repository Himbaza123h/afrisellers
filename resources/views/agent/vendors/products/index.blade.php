@extends('layouts.home')

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('agent.vendors.show', $vendor->id) }}"
               class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Products</h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $vendor->businessProfile?->business_name }} — {{ $stats['total'] }} product{{ $stats['total'] != 1 ? 's' : '' }}
                </p>
            </div>
        </div>
        <a href="{{ route('agent.vendors.products.create', $vendor->id) }}"
           class="inline-flex items-center gap-2 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([
            ['label'=>'Total',    'value'=>$stats['total'],    'color'=>'blue',   'icon'=>'fa-box'],
            ['label'=>'Active',   'value'=>$stats['active'],   'color'=>'green',  'icon'=>'fa-check-circle'],
            ['label'=>'Inactive', 'value'=>$stats['inactive'], 'color'=>'red',    'icon'=>'fa-times-circle'],
            ['label'=>'Draft',    'value'=>$stats['draft'],    'color'=>'yellow', 'icon'=>'fa-edit'],
        ] as $card)
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 bg-{{ $card['color'] }}-100 rounded-lg flex-shrink-0">
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $card['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm text-green-900 flex-1 font-medium">{!! session('success') !!}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm text-red-900 flex-1 font-medium">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('agent.vendors.products.index', $vendor->id) }}" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search products…"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="draft"    {{ request('status') == 'draft'    ? 'selected' : '' }}>Draft</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('agent.vendors.products.index', $vendor->id) }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                <i class="fas fa-undo mr-1"></i> Reset
            </a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Product List</h2>
            <span class="px-2 py-1 text-xs font-semibold text-orange-700 bg-orange-100 rounded-full">
                {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">MOQ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Added</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                        @php
                            $thumb  = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                            $rawUrl = $thumb ? $thumb->getRawOriginal('image_url') : null;
                            $imgSrc = $rawUrl
                                ? (str_starts_with($rawUrl, 'http') ? $rawUrl : asset('storage/' . $rawUrl))
                                : null;
                            $sc = match($product->status) {
                                'active'   => 'bg-green-100 text-green-700',
                                'inactive' => 'bg-red-100 text-red-700',
                                default    => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex items-center justify-center flex-shrink-0 border border-gray-200">
                                        @if($imgSrc)
                                            <img src="{{ $imgSrc }}" alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-image text-gray-300 text-lg"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                        @if($product->short_description)
                                            <p class="text-xs text-gray-400 mt-0.5 max-w-[220px] truncate">
                                                {{ $product->short_description }}
                                            </p>
                                        @endif
                                        @if($product->video_url)
                                            <span class="inline-flex items-center gap-1 text-[10px] text-blue-500 mt-0.5">
                                                <i class="fas fa-video"></i> Has video
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $product->productCategory?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $product->min_order_quantity ?? 1 }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $sc }}">
                                    <i class="fas fa-circle text-[6px]"></i>
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $product->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('agent.vendors.products.show', [$vendor->id, $product->id]) }}"
                                class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg" title="View">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('agent.vendors.products.edit', [$vendor->id, $product->id]) }}"
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <form action="{{ route('agent.vendors.products.destroy', [$vendor->id, $product->id]) }}"
                                    method="POST" class="inline"
                                    onsubmit="return confirm('Permanently delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-box text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No products yet</p>
                                    <p class="text-xs text-gray-400 mt-1 mb-4">Add the first product for this vendor</p>
                                    <a href="{{ route('agent.vendors.products.create', $vendor->id) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700">
                                        <i class="fas fa-plus"></i> Add Product
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                <span class="text-xs text-gray-600">
                    Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}
                </span>
                <div class="text-sm">{{ $products->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
