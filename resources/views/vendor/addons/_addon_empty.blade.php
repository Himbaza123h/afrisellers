{{-- $label – location name or 'any' --}}
<div class="p-20 text-center">
    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4 mx-auto">
        <i class="fas fa-puzzle-piece text-4xl text-gray-300"></i>
    </div>
    <p class="text-base font-semibold text-gray-900 mb-1">No addons available</p>
    <p class="text-sm text-gray-500 mb-6">
        There are no {{ $label === 'any' ? '' : $label }} addons available at the moment.
    </p>
</div>
