@extends('layouts.home')

@section('page-content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.agent-requests.index') }}" class="p-2 text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Agent Request</h1>
            <p class="text-xs text-gray-500">{{ $agentRequest->user->name ?? '' }} &bull; {{ $agentRequest->created_at->format('M d, Y') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600"></i>
            <p class="text-sm font-medium text-green-900">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-600"></i>
            <p class="text-sm font-medium text-red-900">{{ session('error') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="p-3 bg-red-50 rounded-lg border border-red-200">
            @foreach($errors->all() as $e)<p class="text-sm text-red-700">• {{ $e }}</p>@endforeach
        </div>
    @endif

    {{-- Applicant details --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
        <h3 class="text-sm font-semibold text-gray-900 border-b pb-2">Application Details</h3>

        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div>
                <dt class="text-gray-400 text-xs">Full Name</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $agentRequest->user->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs">Email</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $agentRequest->user->email ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs">Phone</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $agentRequest->phone_code }} {{ $agentRequest->phone }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs">Location</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $agentRequest->city }}, {{ $agentRequest->country->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs">Company</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $agentRequest->company_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400 text-xs">Status</dt>
                <dd class="mt-0.5">
                    @php
                        $sc = ['pending'=>'bg-yellow-100 text-yellow-800','approved'=>'bg-green-100 text-green-800','rejected'=>'bg-red-100 text-red-800'];
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $sc[$agentRequest->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($agentRequest->status) }}
                    </span>
                </dd>
            </div>
        </dl>

        @if($agentRequest->motivation)
            <div class="pt-2 border-t">
                <p class="text-xs text-gray-400 mb-1">Motivation</p>
                <p class="text-sm text-gray-700">{{ $agentRequest->motivation }}</p>
            </div>
        @endif

        @if($agentRequest->status === 'rejected' && $agentRequest->rejection_reason)
            <div class="p-3 bg-red-50 border border-red-100 rounded-lg">
                <p class="text-xs font-semibold text-red-700 mb-1">Rejection Reason</p>
                <p class="text-sm text-red-600">{{ $agentRequest->rejection_reason }}</p>
            </div>
        @endif

        @if($agentRequest->responded_at)
            <p class="text-xs text-gray-400">
                Responded on {{ $agentRequest->responded_at->format('M d, Y \a\t H:i') }}
                @if($agentRequest->respondedBy) by {{ $agentRequest->respondedBy->name }} @endif
            </p>
        @endif
    </div>

    {{-- Action buttons — only if pending --}}
    @if($agentRequest->isPending())
    <div class="grid grid-cols-1 gap-4">

        {{-- Approve --}}
        <form action="{{ route('admin.agent-requests.approve', $agentRequest) }}" method="POST"
              onsubmit="return confirm('Approve this request and upgrade the user to agent?')"
              class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            @csrf
            <h3 class="text-sm font-semibold text-gray-900 mb-3">
                <i class="fas fa-check-circle text-green-600 mr-1"></i> Approve Request
            </h3>
            <p class="text-xs text-gray-500 mb-4">
                This will create an agent account for this user, assign the Agent role, and send them an approval email.
            </p>
            <button type="submit"
                class="w-full py-2.5 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition-all">
                Approve &amp; Upgrade Account
            </button>
        </form>

        {{-- Reject --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">
                <i class="fas fa-times-circle text-red-500 mr-1"></i> Reject Request
            </h3>
            <form action="{{ route('admin.agent-requests.reject', $agentRequest) }}" method="POST"
                  class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Reason for rejection <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" rows="3" required minlength="10"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent resize-none"
                        placeholder="Explain why this request cannot be approved...">{{ old('rejection_reason') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">This reason will be sent to the applicant by email.</p>
                </div>
                <button type="submit"
                    class="w-full py-2.5 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-all">
                    Reject &amp; Notify Applicant
                </button>
            </form>
        </div>

    </div>
    @endif

</div>
@endsection
