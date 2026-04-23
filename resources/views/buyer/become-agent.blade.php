@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="mb-2">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Become an AfriSellers Agent</h1>
            <p class="text-sm text-gray-500 mt-1.5">Submit a request to become a sales agent. Our team will review and respond by email.</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl">
                <i class="fas fa-check-circle text-green-500 text-base mt-0.5 shrink-0"></i>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
                <i class="fas fa-exclamation-circle text-red-500 text-base mt-0.5 shrink-0"></i>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl space-y-1">
                @foreach($errors->all() as $e)
                    <p class="text-sm text-red-700 flex items-start gap-2">
                        <span class="mt-0.5 shrink-0">•</span>
                        <span>{{ $e }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        {{-- Application Form --}}
        @if(!$hasPending)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                    <h2 class="text-sm font-semibold text-gray-800">Agent Application Form</h2>
                </div>

                <form action="{{ route('buyer.become-agent.store') }}" method="POST" class="px-6 py-6 space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Company Name --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-gray-700">
                                Company Name
                                <span class="text-gray-400 font-normal ml-1">(optional)</span>
                            </label>
                            <input
                                type="text"
                                name="company_name"
                                value="{{ old('company_name') }}"
                                placeholder="Your company or trading name"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#ff0808]/30 focus:border-[#ff0808] transition"
                            >
                        </div>

                        {{-- Country --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-gray-700">Country</label>
                            <select
                                name="country_id"
                                required
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#ff0808]/30 focus:border-[#ff0808] transition"
                            >
                                <option value="">Select country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Phone Code --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-gray-700">Phone Code</label>
                            <input
                                type="text"
                                name="phone_code"
                                value="{{ old('phone_code', '+250') }}"
                                required
                                placeholder="+250"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#ff0808]/30 focus:border-[#ff0808] transition"
                            >
                        </div>

                        {{-- Phone Number --}}
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-gray-700">Phone Number</label>
                            <input
                                type="text"
                                name="phone"
                                value="{{ old('phone') }}"
                                required
                                placeholder="788 000 000"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#ff0808]/30 focus:border-[#ff0808] transition"
                            >
                        </div>

                        {{-- City --}}
                        <div class="sm:col-span-2 flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-gray-700">City</label>
                            <input
                                type="text"
                                name="city"
                                value="{{ old('city') }}"
                                required
                                placeholder="Kigali"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#ff0808]/30 focus:border-[#ff0808] transition"
                            >
                        </div>

                        {{-- Motivation --}}
                        <div class="sm:col-span-2 flex flex-col gap-1.5">
                            <label class="text-sm font-medium text-gray-700">
                                Why do you want to become an agent?
                                <span class="text-gray-400 font-normal ml-1">(optional)</span>
                            </label>
                            <textarea
                                name="motivation"
                                rows="4"
                                placeholder="Briefly describe your experience and why you'd like to join as an agent..."
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#ff0808]/30 focus:border-[#ff0808] transition resize-none"
                            >{{ old('motivation') }}</textarea>
                        </div>

                    </div>

                    {{-- Submit --}}
                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#ff0808] hover:bg-[#dd0606] text-white text-sm font-semibold rounded-xl shadow-sm transition-all active:scale-[0.98]"
                        >
                            <i class="fas fa-paper-plane text-xs"></i>
                            Submit Application
                        </button>
                    </div>
                </form>
            </div>

        @else
            {{-- Pending State --}}
            <div class="flex items-start gap-4 p-5 bg-amber-50 border border-amber-200 rounded-2xl">
                <div class="shrink-0 w-9 h-9 flex items-center justify-center bg-amber-100 rounded-full">
                    <i class="fas fa-clock text-amber-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-amber-900">Application Under Review</p>
                    <p class="text-sm text-amber-700 mt-1 leading-relaxed">
                        Your agent application is currently being reviewed by our team. You will receive an email once a decision has been made.
                    </p>
                </div>
            </div>
        @endif

        {{-- Request History --}}
        @if($requests->count())
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                    <h2 class="text-sm font-semibold text-gray-800">
                        My Requests
                        <span class="ml-1.5 px-2 py-0.5 bg-gray-200 text-gray-600 text-xs rounded-full font-medium">{{ $requests->count() }}</span>
                    </h2>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($requests as $req)
                        <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

                            <div class="flex-1 space-y-2.5">

                                {{-- Status badge + date --}}
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    @if($req->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            <i class="fas fa-clock text-[10px]"></i> Pending
                                        </span>
                                    @elseif($req->status === 'approved')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle text-[10px]"></i> Approved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle text-[10px]"></i> Rejected
                                        </span>
                                    @endif

                                    <span class="text-xs text-gray-400">{{ $req->created_at->format('M d, Y') }}</span>
                                </div>

                                {{-- Details --}}
                                <div class="space-y-1 text-xs text-gray-600">
                                    <p>
                                        <span class="text-gray-400 font-medium">Location:</span>
                                        <span class="ml-1">{{ $req->city }}, {{ $req->country->name ?? '—' }}</span>
                                    </p>
                                    @if($req->company_name)
                                        <p>
                                            <span class="text-gray-400 font-medium">Company:</span>
                                            <span class="ml-1">{{ $req->company_name }}</span>
                                        </p>
                                    @endif
                                    @if($req->motivation)
                                        <p>
                                            <span class="text-gray-400 font-medium">Message:</span>
                                            <span class="ml-1">{{ $req->motivation }}</span>
                                        </p>
                                    @endif
                                </div>

                                {{-- Rejection reason --}}
                                @if($req->status === 'rejected' && $req->rejection_reason)
                                    <div class="p-3 bg-red-50 border border-red-100 rounded-xl">
                                        <p class="text-xs font-semibold text-red-700 mb-1">Reason for rejection</p>
                                        <p class="text-xs text-red-600 leading-relaxed">{{ $req->rejection_reason }}</p>
                                    </div>
                                @endif

                            </div>

                            {{-- Response date --}}
                            @if($req->responded_at)
                                <div class="shrink-0 text-xs text-gray-400 sm:text-right whitespace-nowrap">
                                    Responded {{ $req->responded_at->format('M d, Y') }}
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
