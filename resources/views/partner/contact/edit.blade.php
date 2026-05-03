@extends('layouts.home')
@section('page-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
            <a href="{{ route('partner.dashboard') }}" class="hover:text-gray-600">Dashboard</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('partner.contact.show') }}" class="hover:text-gray-600">Contact Details</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-gray-600 font-semibold">Edit</span>
        </div>
        <h1 class="text-lg font-black text-gray-900">Edit Contact Details</h1>
    </div>
    <a href="{{ route('partner.contact.show') }}"
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

<form action="{{ route('partner.contact.update') }}" method="POST">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-5 pb-2 border-b border-gray-100">Contact Person</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="contact_name" value="{{ old('contact_name', $partner?->contact_name) }}" required
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                       placeholder="Full name">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Position / Role</label>
                <input type="text" name="contact_position" value="{{ old('contact_position', $partner?->contact_position) }}"
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                       placeholder="e.g. CEO, Partnership Manager">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $partner?->email) }}" required
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                       placeholder="contact@company.com">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $partner?->phone) }}"
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                       placeholder="+250 XXX XXX XXX">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">WhatsApp <span class="text-gray-400 font-normal">(optional)</span></label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $partner?->whatsapp) }}"
                       class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                       placeholder="+250 XXX XXX XXX">
            </div>
        </div>
    </div>
    <div class="mt-4 flex items-center justify-end gap-3">
        <a href="{{ route('partner.contact.show') }}"
           class="px-5 py-2.5 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</a>
        <button type="submit"
                class="px-5 py-2.5 text-xs font-bold text-white bg-[#ff0808] rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </div>
</form>
@endsection
