@extends('layouts.home')

@section('page-content')
<div class="max-w-6xl mx-auto space-y-5">

    {{-- Page header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.partner-requests.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-arrow-left text-gray-600 text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $partnerRequest->company_name }}</h1>
            <p class="text-xs text-gray-500">Partnership Request · Submitted {{ $partnerRequest->created_at->format('M d, Y') }}</p>
        </div>
        @php
            $colors = ['pending' => 'amber', 'approved' => 'green', 'rejected' => 'red'];
            $c = $colors[$partnerRequest->status] ?? 'gray';
        @endphp
        <span class="ml-auto px-3 py-1 rounded-full text-xs font-bold bg-{{ $c }}-100 text-{{ $c }}-700 capitalize">
            {{ $partnerRequest->status }}
        </span>
    </div>

    @if(session('success'))
        <div class="p-3 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600"></i>
            <p class="text-sm text-green-900 flex-1">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Main details card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">

        {{-- Logo + name + type --}}
        <div class="flex items-center gap-4">
            @if($partnerRequest->logo_url)
                <img src="{{ $partnerRequest->logo_url }}" alt="{{ $partnerRequest->company_name }}"
                     class="h-14 w-auto max-w-[140px] object-contain">
            @else
                <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-building text-gray-400 text-xl"></i>
                </div>
            @endif
            <div>
                <h2 class="text-lg font-black text-gray-900">{{ $partnerRequest->company_name }}</h2>
                @if($partnerRequest->partner_type)
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full border border-blue-200">
                        {{ $partnerRequest->partner_type }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Quick stats row --}}
        @if($partnerRequest->established || $partnerRequest->presence_countries || $partnerRequest->country)
        <div class="flex flex-wrap gap-3">
            @if($partnerRequest->established)
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200">
                    <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
                    <span class="text-xs text-gray-700 font-medium">Est. {{ $partnerRequest->established }}</span>
                </div>
            @endif
            @if($partnerRequest->presence_countries)
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200">
                    <i class="fas fa-globe-africa text-gray-400 text-xs"></i>
                    <span class="text-xs text-gray-700 font-medium">
                        Present in {{ $partnerRequest->presence_countries }} {{ Str::plural('country', $partnerRequest->presence_countries) }}
                    </span>
                </div>
            @endif
            @if($partnerRequest->country)
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200">
                    <i class="fas fa-map-marker-alt text-gray-400 text-xs"></i>
                    <span class="text-xs text-gray-700 font-medium">{{ $partnerRequest->country }}</span>
                </div>
            @endif
            @if($partnerRequest->industry)
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200">
                    <i class="fas fa-industry text-gray-400 text-xs"></i>
                    <span class="text-xs text-gray-700 font-medium">{{ $partnerRequest->industry }}</span>
                </div>
            @endif
        </div>
        @endif

        {{-- Contact info grid --}}
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Contact Information</p>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Contact Person</p>
                    <p class="text-gray-900 font-medium">{{ $partnerRequest->contact_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Email</p>
                    <a href="mailto:{{ $partnerRequest->email }}" class="text-blue-600 hover:underline">
                        {{ $partnerRequest->email }}
                    </a>
                </div>
                @if($partnerRequest->phone)
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Phone</p>
                    <a href="tel:{{ $partnerRequest->phone }}" class="text-gray-900">{{ $partnerRequest->phone }}</a>
                </div>
                @endif
                @if($partnerRequest->website_url)
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Website</p>
                    <a href="{{ $partnerRequest->website_url }}" target="_blank"
                       class="text-blue-600 hover:underline break-all">
                        {{ $partnerRequest->website_url }}
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Services offered --}}
        @if($partnerRequest->services && count($partnerRequest->services))
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Services Offered</p>
            <div class="flex flex-wrap gap-2">
                @foreach($partnerRequest->services as $service)
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full border border-blue-100">
                        {{ $service }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- About us --}}
        @if($partnerRequest->about_us)
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">About the Company</p>
            <div class="bg-gray-50 rounded-lg border border-gray-100 p-4 text-sm text-gray-700 leading-relaxed">
                {{ $partnerRequest->about_us }}
            </div>
        </div>
        @endif

        {{-- Intro media --}}
        @if($partnerRequest->intro_url)
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Company Intro</p>
            @php
                $ext = strtolower(pathinfo($partnerRequest->intro, PATHINFO_EXTENSION));
                $isVideo = in_array($ext, ['mp4', 'mov', 'webm']);
            @endphp
            @if($isVideo)
                <video controls class="w-full max-h-64 rounded-lg border border-gray-200 bg-black object-contain">
                    <source src="{{ $partnerRequest->intro_url }}" type="video/{{ $ext === 'mov' ? 'quicktime' : $ext }}">
                    Your browser does not support video playback.
                </video>
            @else
                <img src="{{ $partnerRequest->intro_url }}" alt="Company intro"
                     class="w-full max-h-64 rounded-lg border border-gray-200 object-contain bg-gray-50">
            @endif
        </div>
        @endif

        {{-- Their message --}}
        @if($partnerRequest->message)
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Their Message</p>
            <div class="bg-gray-50 rounded-lg border border-gray-100 p-4 text-sm text-gray-700 leading-relaxed">
                {{ $partnerRequest->message }}
            </div>
        </div>
        @endif

        {{-- Admin notes (if already reviewed) --}}
        @if($partnerRequest->admin_notes)
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Admin Notes</p>
            <div class="bg-yellow-50 rounded-lg border border-yellow-100 p-4 text-sm text-gray-700">
                {{ $partnerRequest->admin_notes }}
            </div>
            @if($partnerRequest->reviewed_at)
                <p class="text-xs text-gray-400 mt-1">
                    Reviewed {{ $partnerRequest->reviewed_at->format('M d, Y H:i') }}
                    @if($partnerRequest->reviewer) by {{ $partnerRequest->reviewer->name }} @endif
                </p>
            @endif
        </div>
        @endif

    </div>

    {{-- Actions --}}
    @if($partnerRequest->isPending())
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Approve --}}
        <form action="{{ route('admin.partner-requests.approve', $partnerRequest) }}" method="POST"
              class="bg-white rounded-xl border border-green-200 p-5 space-y-3">
            @csrf
            <h3 class="text-sm font-bold text-green-700 flex items-center gap-2">
                <i class="fas fa-check-circle"></i> Approve & Add to Partners
            </h3>
            <p class="text-xs text-gray-500">
                This will approve the request and automatically add them to the Partners list on the homepage.
            </p>
            <textarea name="admin_notes" rows="3" placeholder="Optional notes about this approval..."
                      class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 resize-none"></textarea>
            <button type="submit"
                    onclick="return confirm('Approve this request and add them as a partner?')"
                    class="w-full py-2.5 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition-all">
                <i class="fas fa-check mr-2"></i> Approve Request
            </button>
        </form>

        {{-- Reject --}}
        <form action="{{ route('admin.partner-requests.reject', $partnerRequest) }}" method="POST"
              class="bg-white rounded-xl border border-red-200 p-5 space-y-3">
            @csrf
            <h3 class="text-sm font-bold text-red-600 flex items-center gap-2">
                <i class="fas fa-times-circle"></i> Reject Request
            </h3>
            <p class="text-xs text-gray-500">
                The requester can be contacted via the email provided above if needed.
            </p>
            <textarea name="admin_notes" rows="3" placeholder="Reason for rejection (optional)..."
                      class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            <button type="submit"
                    onclick="return confirm('Reject this partnership request?')"
                    class="w-full py-2.5 bg-red-600 text-white text-sm font-bold rounded-lg hover:bg-red-700 transition-all">
                <i class="fas fa-times mr-2"></i> Reject Request
            </button>
        </form>

    </div>

    {{-- Direct contact --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-900">Contact the requester directly</p>
            <p class="text-xs text-gray-500">
                {{ $partnerRequest->email }}
                @if($partnerRequest->phone) · {{ $partnerRequest->phone }} @endif
            </p>
        </div>
        <a href="mailto:{{ $partnerRequest->email }}"
           class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <i class="fas fa-envelope"></i> Send Email
        </a>
    </div>

    @else
    {{-- Already reviewed notice --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
        <i class="fas fa-info-circle text-gray-400 text-lg"></i>
        <p class="text-sm text-gray-600">
            This request was <strong class="capitalize">{{ $partnerRequest->status }}</strong>
            @if($partnerRequest->reviewed_at) on {{ $partnerRequest->reviewed_at->format('M d, Y') }} @endif.
        </p>
        <a href="mailto:{{ $partnerRequest->email }}"
           class="ml-auto px-3 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 flex items-center gap-2 whitespace-nowrap">
            <i class="fas fa-envelope"></i> Contact
        </a>
    </div>
    @endif

</div>
@endsection
