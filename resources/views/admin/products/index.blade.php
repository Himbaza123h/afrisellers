@extends('layouts.home')

@section('page-content')
<!-- Header -->
<div class="mb-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-black text-gray-900 uppercase sm:text-2xl lg:text-lg">Product Management</h1>
            <p class="mt-1 text-xs text-gray-600 sm:text-sm">Manage, approve and review all products</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.vendor.product.index') }}?print=1" target="_blank"
               class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-print"></i> Print
            </a>
            <button onclick="document.getElementById('bulkUploadModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-file-excel"></i> Bulk Upload
            </button>
            <a href="{{ route('admin.vendor.product.downloadTemplate') }}"
               class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-download"></i> Template
            </a>
            <a href="{{ route('admin.vendor.product.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    </div>
</div>

<!-- Alerts -->
@if(session('success'))
    <div class="p-4 mb-4 bg-green-50 rounded-lg border border-green-300">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
@endif
@if(session('error'))
    <div class="p-4 mb-4 bg-red-50 rounded-lg border border-red-300">
        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
    </div>
@endif
@if(session('import_errors'))
    <div class="p-4 mb-4 bg-yellow-50 rounded-lg border border-yellow-300">
        <p class="mb-2 text-sm font-semibold text-yellow-900">Import completed with errors:</p>
        <ul class="space-y-1 text-sm text-yellow-800">
            @foreach(session('import_errors') as $err)
                <li>• {{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-2 gap-4 mb-6 lg:grid-cols-4">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <p class="text-xs font-medium text-gray-500 uppercase">Total Products</p>
        <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <p class="text-xs font-medium text-yellow-600 uppercase">Draft</p>
        <p class="mt-1 text-2xl font-black text-yellow-700">{{ number_format($stats['draft']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <p class="text-xs font-medium text-green-600 uppercase">Active</p>
        <p class="mt-1 text-2xl font-black text-green-700">{{ number_format($stats['active']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <p class="text-xs font-medium text-red-600 uppercase">Inactive</p>
        <p class="mt-1 text-2xl font-black text-red-700">{{ number_format($stats['inactive']) }}</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.vendor.product.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block mb-1 text-xs font-medium text-gray-600">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-[#ff0808]">
        </div>
        <div class="min-w-[140px]">
            <label class="block mb-1 text-xs font-medium text-gray-600">Status</label>
            <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                <option value="">All Status</option>
                <option value="draft"    {{ request('status') === 'draft'    ? 'selected' : '' }}>Draft</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="min-w-[160px]">
            <label class="block mb-1 text-xs font-medium text-gray-600">Category</label>
            <select name="category" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[140px]">
            <label class="block mb-1 text-xs font-medium text-gray-600">Country</label>
            <select name="country" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                <option value="">All Countries</option>
                @foreach($countries as $c)
                    <option value="{{ $c->id }}" {{ request('country') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[130px]">
            <label class="block mb-1 text-xs font-medium text-gray-600">Verified</label>
            <select name="verified" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808]">
                <option value="">All</option>
                <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Verified</option>
                <option value="no"  {{ request('verified') === 'no'  ? 'selected' : '' }}>Unverified</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            <a href="{{ route('admin.vendor.product.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Bulk Actions Form -->
<form id="bulkForm" method="POST" action="">
    @csrf
    <input type="hidden" id="bulkStatus" name="status" value="">
    <div id="bulkIdsContainer"></div>
</form>

<!-- Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <!-- Bulk bar -->
    <div id="bulkBar" class="hidden items-center gap-3 px-4 py-3 bg-blue-50 border-b border-blue-200">
        <span id="selectedCount" class="text-sm font-semibold text-blue-900">0 selected</span>
        <button type="button" onclick="submitBulk('{{ route('admin.bulk-delete') }}', null, 'Delete selected products?')"
                class="px-3 py-1.5 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">
            <i class="fas fa-trash mr-1"></i> Delete Selected
        </button>
        <button type="button" onclick="submitBulk('{{ route('admin.bulk-status') }}', 'approved')"
                class="px-3 py-1.5 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">
            <i class="fas fa-check mr-1"></i> Approve Selected
        </button>
        <button type="button" onclick="submitBulk('{{ route('admin.bulk-status') }}', 'rejected')"
                class="px-3 py-1.5 text-xs font-semibold text-white bg-yellow-600 rounded-lg hover:bg-yellow-700">
            <i class="fas fa-times mr-1"></i> Reject Selected
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-[#ff0808] focus:ring-[#ff0808]">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Product</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Vendor</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Country</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Verified</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="ids[]" value="{{ $product->id }}"
                                   class="row-checkbox rounded border-gray-300 text-[#ff0808] focus:ring-[#ff0808]">
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($product->images->isNotEmpty())
                                    @php
                                        $firstImageRaw = $product->images->first()->image_url ?? '';
                                        $firstImageUrl = str_starts_with($firstImageRaw, 'http')
                                            ? $firstImageRaw
                                            : asset($firstImageRaw);
                                    @endphp
                                    <img src="{{ $firstImageUrl }}"
                                         alt="{{ $product->name }}"
                                         class="w-10 h-10 object-cover rounded-lg border border-gray-200 flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-box text-gray-400 text-xs"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900 line-clamp-1">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->images->count() }} image(s)</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $product->productCategory->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $product->user->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $product->country->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusMap = [
                                    'active'   => 'bg-green-100 text-green-800',
                                    'inactive' => 'bg-red-100 text-red-800',
                                    'draft'    => 'bg-yellow-100 text-yellow-800',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusMap[$product->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($product->is_admin_verified)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700">
                                    <i class="fas fa-check-circle"></i> Yes
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-400">
                                    <i class="fas fa-times-circle"></i> No
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                            {{ $product->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.vendor.product.show', $product) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.vendor.product.edit', $product) }}"
                                   class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-pencil text-xs"></i>
                                </a>
                                @if($product->status === 'draft')
                                    <form action="{{ route('admin.vendor.product.approve', $product) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Approve">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.vendor.product.reject', $product) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Reject">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.vendor.product.destroy', $product) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-16 text-center">
                            <i class="fas fa-box text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500 font-medium">No products found</p>
                            <p class="text-gray-400 text-sm mt-1">Try adjusting your filters or add a new product</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $products->links() }}
        </div>
    @endif
</div>

<!-- ─── Bulk Upload Modal ─── -->
<div id="bulkUploadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Bulk Upload Products</h3>
            <button onclick="document.getElementById('bulkUploadModal').classList.add('hidden')"
                    class="p-1 text-gray-400 hover:text-gray-600 rounded-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.vendor.product.bulk-upload') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800 font-medium mb-2"><i class="fas fa-info-circle mr-1"></i> Before uploading:</p>
                <ul class="text-xs text-blue-700 space-y-1">
                    <li>• Download the template first</li>
                    <li>• Fill in all required columns</li>
                    <li>• Ensure category, country, and vendor emails match existing records</li>
                </ul>
            </div>
            <input type="hidden" name="redirect_vendor" value="{{ request('vendor') }}">
            <div class="mb-4">
                <a href="{{ route('admin.vendor.product.downloadTemplate') }}"
                   class="inline-flex items-center gap-2 w-full justify-center px-4 py-2.5 text-sm font-semibold text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fas fa-download"></i> Download Template
                </a>
            </div>
            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-700">Upload Excel File (.xlsx)</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#ff0808] hover:bg-red-50 transition-colors">
                        <i class="fas fa-file-excel text-gray-400 text-3xl mb-2"></i>
                        <p class="text-sm text-gray-500">Click to select file or drag & drop</p>
                        <p class="text-xs text-gray-400 mt-1">.xlsx files only, max 10MB</p>
                        <input type="file" name="file" accept=".xlsx,.xls" class="hidden"
                               onchange="document.getElementById('uploadFileName').textContent = this.files[0]?.name || ''">
                    </label>
                </div>
                <p id="uploadFileName" class="mt-2 text-xs text-center text-gray-600 font-medium"></p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('bulkUploadModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                    <i class="fas fa-upload mr-1"></i> Upload
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function submitBulk(action, status, confirmMsg) {
    if (confirmMsg && !confirm(confirmMsg)) return;

    const checked = document.querySelectorAll('.row-checkbox:checked');
    if (checked.length === 0) {
        alert('Please select at least one product.');
        return;
    }

    const form = document.getElementById('bulkForm');
    form.action = action;

    document.getElementById('bulkIdsContainer').innerHTML = '';

    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'ids[]';
        input.value = cb.value;
        document.getElementById('bulkIdsContainer').appendChild(input);
    });

    if (status) {
        document.getElementById('bulkStatus').value = status;
    }

    form.submit();
}

const selectAll     = document.getElementById('selectAll');
const checkboxes    = document.querySelectorAll('.row-checkbox');
const bulkBar       = document.getElementById('bulkBar');
const selectedCount = document.getElementById('selectedCount');

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-checkbox:checked').length;
    selectedCount.textContent = checked + ' selected';
    bulkBar.classList.toggle('hidden', checked === 0);
    bulkBar.classList.toggle('flex', checked > 0);
}

selectAll.addEventListener('change', () => {
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateBulkBar();
});

checkboxes.forEach(cb => cb.addEventListener('change', updateBulkBar));
</script>
@endsection
