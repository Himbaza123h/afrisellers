@extends('layouts.home')

@section('page-content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('vendor.showrooms.show', $showroom->id) }}"
           class="w-8 h-8 flex items-center justify-center rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-black text-gray-900 uppercase lg:text-lg">Manage Products</h1>
    </div>
    <p class="text-sm text-gray-600 ml-11">{{ $showroom->name }}</p>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="p-4 mb-6 bg-green-50 rounded-md border border-green-300">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="p-4 mb-6 bg-red-50 rounded-md border border-red-300">
        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Products in Showroom -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Products in Showroom ({{ $showroomProducts->total() }})</h2>
            </div>
            @if($showroomProducts->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-box text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 mb-2">No products in this showroom yet</p>
                    <p class="text-sm text-gray-500">Add products from the available list on the right</p>
                </div>
            @else
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($showroomProducts as $product)
                            <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                                <div class="flex gap-4 p-4">
                                    <div class="w-20 h-20 bg-gray-100 rounded-md overflow-hidden flex-shrink-0">

                                        @if($product->images->first())
                                            <img src="{{ $product->images->first()->image_url }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-image text-gray-300"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 text-sm line-clamp-2 mb-1">{{ $product->name }}</h3>
                                        <p class="text-xs text-gray-500 mb-2">{{ $product->productCategory->name ?? 'Uncategorized' }}</p>
                                        <button onclick="removeProduct({{ $product->id }})"
                                                class="text-xs text-red-600 hover:text-red-700 font-medium">
                                            <i class="fas fa-times mr-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @if($showroomProducts->hasPages())
                    <div class="mt-6">
                        {{ $showroomProducts->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Available Products -->
<div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Available Products</h2>
            <p class="text-xs text-gray-500 mt-1">{{ $availableProducts->count() }} products available</p>
        </div>
        @if($availableProducts->isEmpty())
            <div class="p-8 text-center">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-box-open text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-600 mb-3">All your products are already in this showroom</p>
                <a href="{{ route('vendor.product.create') }}"
                   class="inline-flex items-center text-sm text-purple-600 hover:text-purple-700 font-medium">
                    <i class="fas fa-plus mr-1"></i>Create New Product
                </a>
            </div>
        @else
            <div class="p-4 max-h-[600px] overflow-y-auto">
                <div class="space-y-2">
                    @foreach($availableProducts as $product)
                        <div class="flex gap-3 p-3 border border-gray-200 rounded-lg hover:border-purple-300 transition-colors">
                            <div class="w-12 h-12 bg-gray-100 rounded-md overflow-hidden flex-shrink-0">
                                @if($product->images->first())
                                    <img src="{{ $product->images->first()->image_url }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-gray-300 text-xs"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 text-xs line-clamp-1 mb-1">{{ $product->name }}</h4>
                                <p class="text-[10px] text-gray-500 mb-2">{{ $product->productCategory->name ?? 'Uncategorized' }}</p>
                                <button onclick="addProduct({{ $product->id }})"
                                        class="text-[10px] text-purple-600 hover:text-purple-700 font-medium">
                                    <i class="fas fa-plus mr-1"></i>Add to Showroom
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="mt-6 bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl border border-purple-200 p-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-purple-600 text-lg mt-0.5"></i>
            <div>
                <h3 class="text-sm font-bold text-purple-900 mb-2">Quick Stats</h3>
                <div class="space-y-2 text-xs text-purple-800">
                    <div class="flex justify-between">
                        <span>In Showroom:</span>
                        <span class="font-bold">{{ $showroomProducts->total() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Available:</span>
                        <span class="font-bold">{{ $availableProducts->count() }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-purple-200">
                        <span>Total Products:</span>
                        <span class="font-bold">{{ $showroomProducts->total() + $availableProducts->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Add Product Form -->
<form id="add-product-form" action="{{ route('vendor.showrooms.products.add', $showroom->id) }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="product_id" id="product-id-to-add">
</form>
<!-- Remove Product Form -->
<form id="remove-product-form" action="{{ route('vendor.showrooms.products.remove', $showroom->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
    <input type="hidden" name="product_id" id="product-id-to-remove">
</form>
<script>
function addProduct(productId) {
    if (confirm('Add this product to the showroom?')) {
        document.getElementById('product-id-to-add').value = productId;
        document.getElementById('add-product-form').submit();
    }
}

function removeProduct(productId) {
    if (confirm('Remove this product from the showroom?')) {
        document.getElementById('product-id-to-remove').value = productId;
        document.getElementById('remove-product-form').submit();
    }
}
</script>
@endsection
