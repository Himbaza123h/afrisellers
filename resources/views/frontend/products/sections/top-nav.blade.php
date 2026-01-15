<div class="bg-gray-100 border-b sticky top-0 z-40">
    <div class="container mx-auto px-4">
        <div class="flex items-center gap-8 py-4 overflow-x-auto">
            <a href="{{ route('products.search', ['type' => $type, 'slug' => $slug, 'tab' => 'products']) }}{{ request()->has('sort') ? '&sort=' . request('sort') : '' }}"
               class="tab-btn whitespace-nowrap font-semibold pb-2 border-b-2 transition-colors {{ $tab === 'products' ? 'text-[#ff0808] border-[#ff0808]' : 'text-gray-500 border-transparent hover:text-gray-900' }}">
                Products {{ $totalProducts > 0 ? '(' . $totalProducts . ')' : '' }}
            </a>
            <a href="{{ route('products.search', ['type' => $type, 'slug' => $slug, 'tab' => 'suppliers']) }}{{ request()->has('sort') ? '&sort=' . request('sort') : '' }}"
               class="tab-btn whitespace-nowrap font-semibold pb-2 border-b-2 transition-colors {{ $tab === 'suppliers' ? 'text-[#ff0808] border-[#ff0808]' : 'text-gray-500 border-transparent hover:text-gray-900' }}">
                Suppliers {{ $totalSuppliers > 0 ? '(' . $totalSuppliers . ')' : '' }}
            </a>
            <a href="{{ route('products.search', ['type' => $type, 'slug' => $slug, 'tab' => 'worldwide']) }}{{ request()->has('sort') ? '&sort=' . request('sort') : '' }}"
               class="tab-btn whitespace-nowrap font-semibold pb-2 border-b-2 transition-colors {{ $tab === 'worldwide' ? 'text-[#ff0808] border-[#ff0808]' : 'text-gray-500 border-transparent hover:text-gray-900' }}">
                Worldwide
            </a>
        </div>
    </div>
</div>
