@php
    $inCompare = in_array($product->id, session('compare_products', []));
    $currentUrl = url()->current();
@endphp

@if($inCompare)
    <form action="{{ route('buyer.compare.remove', $product->id) }}" method="POST" class="w-full">
        @csrf @method('DELETE')
        <input type="hidden" name="redirect" value="{{ $currentUrl }}">
        <button type="submit"
            class="w-full bg-green-600 hover:bg-red-600 text-white font-bold py-2 sm:py-2.5 px-3 sm:px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl text-xs sm:text-sm md:text-base inline-flex items-center justify-center gap-1.5 sm:gap-2">
            <i class="fas fa-check-circle"></i>
            In Compare
        </button>
    </form>
@else
    <form action="{{ route('buyer.compare.add', $product->id) }}" method="POST" class="w-full">
        @csrf
        <input type="hidden" name="redirect" value="{{ $currentUrl }}">
        <button type="submit"
            class="w-full bg-white hover:bg-blue-50 text-gray-700 hover:text-blue-600 font-bold py-2 sm:py-2.5 px-3 sm:px-4 rounded-lg border-2 border-gray-300 hover:border-blue-400 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl text-xs sm:text-sm md:text-base inline-flex items-center justify-center gap-1.5 sm:gap-2">
            <i class="fas fa-balance-scale"></i>
            Compare
        </button>
    </form>
@endif
