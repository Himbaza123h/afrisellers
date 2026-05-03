@extends('layouts.home')
@section('page-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('partner.support.index') }}" class="hover:text-gray-600">Support</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">New Ticket</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Create Support Ticket</h1>
        <p class="text-xs text-gray-500 mt-0.5">We typically respond within 24 hours</p>
    </div>
    <a href="{{ route('partner.support.tickets') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition-all">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
            <ul class="text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('partner.support.ticket.store') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider pb-2 border-b border-gray-100">Ticket Details</h2>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Subject <span class="text-red-500">*</span></label>
            <input type="text" name="subject" value="{{ old('subject') }}" required
                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                   placeholder="Brief summary of your issue">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                <select name="category" required
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                    <option value="">Select category...</option>
                    @foreach(['general' => 'General', 'billing' => 'Billing', 'technical' => 'Technical', 'partnership' => 'Partnership', 'other' => 'Other'] as $val => $label)
                        <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Priority <span class="text-red-500">*</span></label>
                <select name="priority" required
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                    <option value="">Select priority...</option>
                    <option value="low"    {{ old('priority') === 'low'    ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high"   {{ old('priority') === 'high'   ? 'selected' : '' }}>High</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Message <span class="text-red-500">*</span></label>
            <textarea name="message" rows="6" required
                      class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent resize-none"
                      placeholder="Describe your issue in detail...">{{ old('message') }}</textarea>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-end gap-3">
        <a href="{{ route('partner.support.tickets') }}"
           class="px-5 py-2.5 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</a>
        <button type="submit"
                class="px-5 py-2.5 text-xs font-bold text-white bg-[#ff0808] rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
            <i class="fas fa-paper-plane"></i> Submit Ticket
        </button>
    </div>
</form>
@endsection
