@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Product Comparison</h1>
            <p class="text-sm text-gray-500 mt-1">Compare up to 4 products side by side</p>
        </div>
        <div class="flex items-center gap-2">
            @if($products->count())
                <form action="{{ route('buyer.compare.clear') }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Clear all products from comparison?')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-200 text-red-600 rounded-lg hover:bg-red-50 text-sm font-medium shadow-sm">
                        <i class="fas fa-trash"></i> Clear All
                    </button>
                </form>
            @endif
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm">
                <i class="fas fa-plus"></i> Add Products
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @foreach(['success','error','info'] as $type)
        @if(session($type))
            @php
                $colors = ['success'=>'green','error'=>'red','info'=>'blue'];
                $icons  = ['success'=>'check-circle','error'=>'exclamation-circle','info'=>'info-circle'];
                $c = $colors[$type]; $i = $icons[$type];
            @endphp
            <div class="flex items-start gap-3 p-4 bg-{{ $c }}-50 border border-{{ $c }}-200 rounded-xl">
                <i class="fas fa-{{ $i }} text-{{ $c }}-500 mt-0.5 shrink-0"></i>
                <p class="text-sm font-medium text-{{ $c }}-800">{{ session($type) }}</p>
            </div>
        @endif
    @endforeach

    @if($products->isEmpty())
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-16 text-center">
            <div class="flex flex-col items-center gap-4">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-balance-scale text-3xl text-gray-300"></i>
                </div>
                <div>
                    <p class="text-lg font-semibold text-gray-800">No products to compare</p>
                    <p class="text-sm text-gray-500 mt-1">Browse products and click "Add to Compare" to get started.</p>
                </div>
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition">
                    <i class="fas fa-search"></i> Browse Products
                </a>
            </div>
        </div>

    @else
        {{-- Comparison Table --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px]">

                    {{-- Product Headers --}}
                    <thead>
                        <tr class="border-b border-gray-100">
                            <td class="px-6 py-5 w-40 bg-gray-50 text-xs font-semibold text-gray-500 uppercase align-top">
                                Product
                            </td>
                            @foreach($products as $product)
                                <td class="px-6 py-5 align-top border-l border-gray-100 text-center">
                                    {{-- Remove Button --}}
                                    <form action="{{ route('buyer.compare.remove', $product->id) }}" method="POST" class="flex justify-end mb-2">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-gray-400 hover:text-red-500 transition flex items-center gap-1">
                                            <i class="fas fa-times"></i> Remove
                                        </button>
                                    </form>

                                    {{-- Image --}}
                                    @php $img = $product->images->first(); @endphp
                                    <div class="w-24 h-24 mx-auto rounded-xl overflow-hidden bg-gray-100 mb-3 flex items-center justify-center">
                                        @if($img)
                                            <img src="{{ $img->image_url }}" alt="{{ $img->alt_text ?? $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-box text-3xl text-gray-300"></i>
                                        @endif
                                    </div>

                                    {{-- Name --}}
                                    <p class="text-sm font-bold text-gray-900 leading-snug">{{ $product->name }}</p>

                                    {{-- Category --}}
                                    @if($product->productCategory)
                                        <p class="text-xs text-gray-400 mt-1">{{ $product->productCategory->name }}</p>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Empty slot(s) --}}
                            @for($i = $products->count(); $i < 4; $i++)
                                <td class="px-6 py-5 border-l border-dashed border-gray-200 text-center align-middle">
                                    <div class="flex flex-col items-center gap-3 py-8 text-gray-300">
                                        <div class="w-16 h-16 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center">
                                            <i class="fas fa-plus text-xl"></i>
                                        </div>
                                        <p class="text-xs">Add a product</p>
                                    </div>
                                </td>
                            @endfor
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-50">

                        {{-- ── Country ───────────────────────────────── --}}
                        <tr>
                            <td class="px-6 py-4 bg-gray-50 text-xs font-semibold text-gray-500 uppercase">Country</td>
                            @foreach($products as $product)
                                <td class="px-6 py-4 text-center border-l border-gray-100">
                                    <span class="text-sm text-gray-700">{{ $product->country?->name ?? '—' }}</span>
                                </td>
                            @endforeach
                            @for($i = $products->count(); $i < 4; $i++)
                                <td class="px-6 py-4 border-l border-dashed border-gray-200"></td>
                            @endfor
                        </tr>

                        {{-- ── Min Order ─────────────────────────────── --}}
                        <tr>
                            <td class="px-6 py-4 bg-gray-50 text-xs font-semibold text-gray-500 uppercase">Min. Order</td>
                            @foreach($products as $product)
                                <td class="px-6 py-4 text-center border-l border-gray-100">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $product->min_order_quantity ? number_format($product->min_order_quantity) . ' units' : '—' }}
                                    </span>
                                </td>
                            @endforeach
                            @for($i = $products->count(); $i < 4; $i++)
                                <td class="px-6 py-4 border-l border-dashed border-gray-200"></td>
                            @endfor
                        </tr>

                        {{-- ── Negotiable ────────────────────────────── --}}
                        <tr>
                            <td class="px-6 py-4 bg-gray-50 text-xs font-semibold text-gray-500 uppercase">Negotiable</td>
                            @foreach($products as $product)
                                <td class="px-6 py-4 text-center border-l border-gray-100">
                                    @if($product->is_negotiable)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                            <i class="fas fa-check text-[10px]"></i> Yes
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-semibold">
                                            <i class="fas fa-times text-[10px]"></i> No
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                            @for($i = $products->count(); $i < 4; $i++)
                                <td class="px-6 py-4 border-l border-dashed border-gray-200"></td>
                            @endfor
                        </tr>

                        {{-- ── Pricing Tiers ─────────────────────────── --}}
                        <tr>
                            <td class="px-6 py-4 bg-gray-50 text-xs font-semibold text-gray-500 uppercase align-top">
                                Pricing Tiers
                            </td>
                            @foreach($products as $product)
                                <td class="px-6 py-4 border-l border-gray-100 align-top">
                                    @if($product->prices->isEmpty())
                                        <p class="text-xs text-gray-400 text-center">No pricing listed</p>
                                    @else
                                        <div class="space-y-2">
                                            @foreach($product->prices as $price)
                                                <div class="flex items-center justify-between gap-2 p-2 bg-gray-50 rounded-lg border border-gray-100">
                                                    <span class="text-[11px] text-gray-500">
                                                        {{ $price->quantity_range }}
                                                    </span>
                                                    <span class="text-sm font-bold text-[#ff0808]">
                                                        {{ $price->formatted_price }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                            @for($i = $products->count(); $i < 4; $i++)
                                <td class="px-6 py-4 border-l border-dashed border-gray-200"></td>
                            @endfor
                        </tr>

                        {{-- ── Variations ────────────────────────────── --}}
                        <tr>
                            <td class="px-6 py-4 bg-gray-50 text-xs font-semibold text-gray-500 uppercase align-top">
                                Variations
                            </td>
                            @foreach($products as $product)
                                <td class="px-6 py-4 border-l border-gray-100 align-top">
                                    @if($product->variations->isEmpty())
                                        <p class="text-xs text-gray-400 text-center">None</p>
                                    @else
                                        @foreach($product->variations->groupBy('variation_type') as $type => $items)
                                            <div class="mb-2">
                                                <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">{{ $type }}</p>
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($items as $v)
                                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[11px] rounded-full border border-blue-100">
                                                            {{ $v->variation_value }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </td>
                            @endforeach
                            @for($i = $products->count(); $i < 4; $i++)
                                <td class="px-6 py-4 border-l border-dashed border-gray-200"></td>
                            @endfor
                        </tr>

                        {{-- ── Description ───────────────────────────── --}}
                        <tr>
                            <td class="px-6 py-4 bg-gray-50 text-xs font-semibold text-gray-500 uppercase align-top">
                                Description
                            </td>
                            @foreach($products as $product)
                                <td class="px-6 py-4 border-l border-gray-100 align-top">
                                    <p class="text-xs text-gray-600 leading-relaxed">
                                        {{ $product->short_description ?? \Str::limit(strip_tags($product->description), 120) }}
                                    </p>
                                </td>
                            @endforeach
                            @for($i = $products->count(); $i < 4; $i++)
                                <td class="px-6 py-4 border-l border-dashed border-gray-200"></td>
                            @endfor
                        </tr>

                        {{-- ── Action Row ────────────────────────────── --}}
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Inquire</td>
                            @foreach($products as $product)
                                <td class="px-6 py-4 border-l border-gray-100 text-center">
                                    <a href="{{ url('/products/' . $product->id) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition">
                                        <i class="fas fa-eye"></i> View Product
                                    </a>
                                </td>
                            @endforeach
                            @for($i = $products->count(); $i < 4; $i++)
                                <td class="px-6 py-4 border-l border-dashed border-gray-200"></td>
                            @endfor
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
</div>
@endsection
