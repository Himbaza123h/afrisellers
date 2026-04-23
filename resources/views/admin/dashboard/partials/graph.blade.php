<!-- Chart Section -->
<div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
        <h3 class="text-sm font-bold text-gray-900">Regional Performance</h3>
        <div class="flex gap-1.5">
            <button class="px-3 py-1 text-xs font-bold text-white rounded-md bg-[#ff0808]">Weekly</button>
            <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-md">Monthly</button>
            <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-md">Yearly</button>
        </div>
    </div>
    <div class="h-96">
        <canvas id="regionalChartGraph"></canvas>
    </div>
</div>
