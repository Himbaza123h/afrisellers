@extends('layouts.home')

@section('page-content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('agent.support.index') }}"
           class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Open Support Ticket</h1>
            <p class="text-xs text-gray-500 mt-0.5">Describe your issue and we'll get back to you</p>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('agent.support.ticket.store') }}" method="POST" class="space-y-5">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">

            {{-- Subject --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Subject <span class="text-red-500">*</span>
                </label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                    placeholder="Brief description of your issue"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('subject')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

{{-- Category --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Category <span class="text-red-500">*</span>
                </label>
                <select name="category" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a category</option>
                    @foreach([
                        'general'   => 'General Inquiry',
                        'technical' => 'Technical Issue',
                        'billing'   => 'Billing & Payments',
                        'vendor'    => 'Vendor Management',
                        'account'   => 'Account & Profile',
                        'other'     => 'Other',
                    ] as $val => $label)
                        <option value="{{ $val }}" {{ old('category') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" rows="7" required
                    placeholder="Please describe your issue in detail. Include any error messages, steps to reproduce, or relevant context…"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 leading-relaxed resize-none">{{ old('description') }}</textarea>
                <p class="mt-1 text-xs text-gray-400">The more detail you provide, the faster we can help.</p>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

                {{-- Requires Attention --}}
        <div class="flex items-start gap-3 p-4 bg-amber-50 rounded-xl border border-amber-200">
            <input type="checkbox" name="requires_attention" id="requiresAttention" value="1"
                {{ old('requires_attention') ? 'checked' : '' }}
                class="mt-0.5 rounded border-gray-300 text-amber-500 focus:ring-amber-400">
            <div>
                <label for="requiresAttention" class="text-sm font-semibold text-gray-800 cursor-pointer flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-amber-500"></i>
                    Requires Admin Attention
                </label>
                <p class="text-xs text-gray-500 mt-0.5">
                    Flag this ticket as urgent — it will be highlighted and prioritised in the admin queue.
                </p>
            </div>
        </div>

        {{-- Tip --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
            <i class="fas fa-lightbulb text-blue-400 mt-0.5 flex-shrink-0"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Before submitting</p>
                <p>Check our <a href="{{ route('agent.support.faq') }}" class="underline font-medium">FAQ</a>
                — your question may already be answered there. Our typical response time is within 24 hours on business days.</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('agent.support.index') }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 shadow-md">
                <i class="fas fa-paper-plane"></i> Submit Ticket
            </button>
        </div>
    </form>
</div>
@endsection
