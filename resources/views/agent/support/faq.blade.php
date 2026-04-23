@extends('layouts.home')

@section('page-content')
<div class="space-y-5 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('agent.support.index') }}"
           class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Frequently Asked Questions</h1>
            <p class="text-xs text-gray-500 mt-0.5">Browse answers to common questions</p>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('agent.support.faq') }}" class="flex gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search FAQs…"
                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <select name="category"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                        {{ ucfirst($cat) }}
                    </option>
                @endforeach
            </select>
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                Search
            </button>
        </form>
    </div>

    {{-- FAQs grouped by category --}}
    @forelse($faqs as $category => $items)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                <h2 class="text-sm font-bold text-gray-700 capitalize flex items-center gap-2">
                    <i class="fas fa-folder text-blue-400 text-xs"></i>
                    {{ ucfirst($category) }}
                    <span class="ml-auto px-2 py-0.5 bg-gray-200 text-gray-600 text-[10px] font-semibold rounded-full">
                        {{ $items->count() }}
                    </span>
                </h2>
            </div>

            <div class="divide-y divide-gray-50" x-data="{ open: null }">
                @foreach($items as $i => $faq)
                    <div class="faq-item">
                        <button type="button"
                            onclick="toggleFaq('faq-{{ $faq->id }}')"
                            class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition-colors group">
                            <p class="text-sm font-semibold text-gray-800 pr-4 group-hover:text-blue-700 transition-colors">
                                {{ $faq->question }}
                            </p>
                            <i id="icon-faq-{{ $faq->id }}"
                               class="fas fa-chevron-down text-gray-400 text-xs flex-shrink-0 transition-transform duration-200"></i>
                        </button>
                        <div id="faq-{{ $faq->id }}" class="hidden px-5 pb-5">
                            <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed whitespace-pre-wrap">
                                {{ $faq->answer }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col items-center py-16">
            <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-question-circle text-3xl text-gray-300"></i>
            </div>
            <p class="text-gray-500 font-medium">No FAQs found</p>
            <p class="text-xs text-gray-400 mt-1 mb-5">
                {{ request('search') ? 'Try a different search term.' : 'FAQs will appear here once added.' }}
            </p>
            <a href="{{ route('agent.support.ticket.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium hover:bg-red-700">
                <i class="fas fa-ticket-alt"></i> Open a Support Ticket
            </a>
        </div>
    @endforelse

    {{-- CTA --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 flex items-center justify-between gap-4">
        <div>
            <p class="text-sm font-bold text-blue-900">Didn't find your answer?</p>
            <p class="text-xs text-blue-700 mt-0.5">Open a support ticket and our team will help you.</p>
        </div>
        <a href="{{ route('agent.support.ticket.create') }}"
           class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-sm">
            <i class="fas fa-ticket-alt"></i> Open Ticket
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFaq(id) {
    const el   = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    const open = !el.classList.contains('hidden');
    el.classList.toggle('hidden', open);
    icon.style.transform = open ? '' : 'rotate(180deg)';
}
</script>
@endpush
