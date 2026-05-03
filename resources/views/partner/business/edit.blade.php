@extends('layouts.home')
@section('page-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('partner.business.show') }}" class="hover:text-gray-600">Business Type</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Edit</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Edit Business Type</h1>
    </div>
    <a href="{{ route('partner.business.show') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<form action="{{ route('partner.business.update') }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider pb-2 border-b border-gray-100">Classification</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Industry</label>
                <input type="text" name="industry" value="{{ old('industry', $partner?->industry) }}"
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                       placeholder="e.g. Banking, Fintech, Retail">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Business Type</label>
                <select name="business_type"
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                    <option value="">Select type...</option>
                    @foreach(['Corporation','SME','Startup'] as $type)
                        <option value="{{ $type }}" {{ old('business_type', $partner?->business_type) === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Services Offered</label>
            @php $services = is_array($partner?->services) ? $partner->services : []; @endphp
            <div id="services-wrapper"
                 class="w-full min-h-[42px] flex flex-wrap gap-1.5 px-3 py-2 border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-[#ff0808] cursor-text"
                 onclick="document.getElementById('services-input').focus()">
                <input id="services-input" type="text"
                       class="flex-1 min-w-[120px] text-sm outline-none bg-transparent"
                       placeholder="Type a service and press Enter…">
            </div>
            <input type="hidden" name="services_raw" id="services-raw"
                   value="{{ old('services_raw', implode(',', $services)) }}">
            <p class="text-xs text-gray-400 mt-1">
                Press <kbd class="bg-gray-100 px-1 rounded">Enter</kbd> or
                <kbd class="bg-gray-100 px-1 rounded">,</kbd> to add each service.
            </p>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-end gap-3">
        <a href="{{ route('partner.business.show') }}"
           class="px-5 py-2.5 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</a>
        <button type="submit"
                class="px-5 py-2.5 text-xs font-bold text-white bg-[#ff0808] rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </div>
</form>

<script>
(function () {
    const wrapper   = document.getElementById('services-wrapper');
    const textInput = document.getElementById('services-input');
    const hidden    = document.getElementById('services-raw');
    let tags = hidden.value ? hidden.value.split(',').map(s => s.trim()).filter(Boolean) : [];

    function render() {
        wrapper.querySelectorAll('.tag-pill').forEach(el => el.remove());
        tags.forEach((tag, i) => {
            const pill = document.createElement('span');
            pill.className = 'tag-pill inline-flex items-center gap-1 px-2 py-0.5 bg-red-50 text-[#ff0808] text-xs font-semibold rounded-md';
            pill.innerHTML = `${tag} <button type="button" data-i="${i}">&times;</button>`;
            pill.querySelector('button').addEventListener('click', () => { tags.splice(i, 1); render(); });
            wrapper.insertBefore(pill, textInput);
        });
        hidden.value = tags.join(',');
    }

    textInput.addEventListener('keydown', function (e) {
        if ((e.key === 'Enter' || e.key === ',') && this.value.trim()) {
            e.preventDefault();
            tags.push(this.value.trim());
            this.value = '';
            render();
        }
        if (e.key === 'Backspace' && !this.value && tags.length) {
            tags.pop();
            render();
        }
    });

    render();
})();
</script>
@endsection
