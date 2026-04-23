@extends('layouts.home')

@section('page-content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- ── Breadcrumb ──────────────────────────────────────────── --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('vendor.ads.index') }}" class="hover:text-blue-600 transition-colors">Ads & Promotions</a>
        <i class="fas fa-chevron-right text-xs text-gray-300"></i>
        <span class="text-gray-900 font-semibold truncate max-w-xs">{{ $ad->title }}</span>
    </nav>

    {{-- ── Flash --}}
    @if(session('success'))
    <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
        <i class="fas fa-check-circle mt-0.5 text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    {{-- ── Subscription Warning ─────────────────────────────────── --}}
    @if(!$subscription || $subscription->status !== 'active')
    <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm">
        <i class="fas fa-exclamation-triangle mt-0.5 text-amber-500 flex-shrink-0"></i>
        <div class="flex-1">
            <span class="font-bold">Subscription Inactive</span> — This ad is paused because your subscription has expired or is not active.
        </div>
        <a href="{{ route('vendor.subscriptions.index') }}" class="text-xs font-bold text-amber-700 underline whitespace-nowrap">Renew Now</a>
    </div>
    @endif

    {{-- ── Main Grid ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Media Preview ──────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                {{-- Media --}}
                <div class="relative bg-gray-950 flex items-center justify-center min-h-[280px]">
                    @if($ad->media_type === 'image')
                        <img src="{{ asset('storage/' . $ad->media_path) }}"
                             alt="{{ $ad->title }}"
                             class="max-h-[360px] w-full object-contain">
                    @else
                        <video controls class="max-h-[360px] w-full object-contain bg-black">
                            <source src="{{ asset('storage/' . $ad->media_path) }}" type="video/mp4">
                            Your browser does not support video playback.
                        </video>
                    @endif

                    {{-- Badges overlay --}}
                    <div class="absolute top-4 left-4 flex gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold
                            {{ $ad->media_type === 'video' ? 'bg-purple-600 text-white' : 'bg-blue-600 text-white' }}">
                            <i class="fas fa-{{ $ad->media_type === 'video' ? 'video' : 'image' }} text-[9px]"></i>
                            {{ strtoupper($ad->media_type) }}
                        </span>
                    </div>
                </div>

                {{-- Title & desc --}}
                <div class="p-6 border-t border-gray-100">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h1 class="text-xl font-bold text-gray-900 mb-1">{{ $ad->title }}</h1>
                            <p class="text-sm text-gray-500">{{ $ad->description ?? 'No description provided.' }}</p>
                        </div>
                        @php
                            $statusColors = [
                                'active'   => 'bg-green-100 text-green-800 border-green-200',
                                'draft'    => 'bg-gray-100 text-gray-600 border-gray-200',
                                'paused'   => 'bg-orange-100 text-orange-800 border-orange-200',
                                'expired'  => 'bg-red-100 text-red-700 border-red-200',
                                'rejected' => 'bg-red-100 text-red-700 border-red-200',
                            ];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 border rounded-full text-xs font-bold {{ $statusColors[$ad->status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $ad->status === 'active' ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}"></span>
                            {{ ucfirst($ad->status) }}
                        </span>
                    </div>

                    @if($ad->target_url)
                    <a href="{{ $ad->target_url }}" target="_blank"
                       class="inline-flex items-center gap-2 mt-3 text-xs text-blue-600 hover:text-blue-700 font-medium">
                        <i class="fas fa-external-link-alt text-[10px]"></i>
                        {{ $ad->target_url }}
                    </a>
                    @endif
                </div>
            </div>

            {{-- Performance ──────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Performance</h2>
                <div class="grid grid-cols-3 gap-4">
                    @foreach([
                        ['label'=>'Impressions','value'=>number_format($ad->impressions),'icon'=>'eye','color'=>'blue'],
                        ['label'=>'Clicks','value'=>number_format($ad->clicks),'icon'=>'mouse-pointer','color'=>'green'],
                        ['label'=>'CTR','value'=>$ad->ctr . '%','icon'=>'chart-line','color'=>'purple'],
                    ] as $m)
                    <div class="p-4 bg-{{ $m['color'] }}-50 rounded-xl text-center border border-{{ $m['color'] }}-100">
                        <div class="w-9 h-9 bg-{{ $m['color'] }}-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-{{ $m['icon'] }} text-{{ $m['color'] }}-600 text-sm"></i>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ $m['value'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $m['label'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar Info ─────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Actions --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 space-y-2.5">
                <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Actions</h2>

                <a href="{{ route('vendor.ads.edit', $ad) }}"
                   class="w-full flex items-center gap-3 px-4 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition-all">
                    <i class="fas fa-edit w-4 text-center"></i> Edit Ad
                </a>

                <form action="{{ route('vendor.ads.toggle-status', $ad) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold text-sm transition-all
                        {{ $ad->status === 'active' ? 'bg-orange-50 text-orange-700 hover:bg-orange-100 border border-orange-200' : 'bg-green-50 text-green-700 hover:bg-green-100 border border-green-200' }}">
                        <i class="fas fa-{{ $ad->status === 'active' ? 'pause' : 'play' }} w-4 text-center"></i>
                        {{ $ad->status === 'active' ? 'Pause Ad' : 'Activate Ad' }}
                    </button>
                </form>

                <form action="{{ route('vendor.ads.destroy', $ad) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to permanently delete this ad?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2.5 bg-red-50 text-red-700 border border-red-200 rounded-xl font-semibold text-sm hover:bg-red-100 transition-all">
                        <i class="fas fa-trash w-4 text-center"></i> Delete Ad
                    </button>
                </form>
            </div>

            {{-- Details --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Ad Details</h2>
                <dl class="space-y-3">
                    @foreach([
                        ['Placement',   ucfirst($ad->placement),   'map-marker-alt'],
                        ['Media Size',  $ad->media_size_formatted, 'hdd'],
                        ['Original Name', $ad->media_original_name ?? '—', 'file'],
                        ['Created',     $ad->created_at->format('M d, Y'), 'calendar'],
                        ['Starts',      $ad->starts_at ? $ad->starts_at->format('M d, Y H:i') : 'Immediately', 'play'],
                        ['Ends',        $ad->ends_at ? $ad->ends_at->format('M d, Y H:i') : 'No expiry', 'stop'],
                    ] as [$label, $value, $icon])
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-{{ $icon }} text-gray-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">{{ $label }}</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $value }}</p>
                        </div>
                    </div>
                    @endforeach
                </dl>
            </div>

            {{-- Approval status --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Approval</h2>
                @if($ad->is_admin_approved)
                    <div class="flex items-center gap-2 text-green-700">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-green-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Approved</p>
                            <p class="text-xs text-gray-400">{{ $ad->approved_at ? $ad->approved_at->format('M d, Y') : '' }}</p>
                        </div>
                    </div>
                @elseif($ad->rejection_reason)
                    <div class="flex items-start gap-2 text-red-700">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-times text-red-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Rejected</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $ad->rejection_reason }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2 text-orange-700">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-orange-500 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Pending Review</p>
                            <p class="text-xs text-gray-400">Awaiting admin approval</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Subscription link --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Subscription</h2>
                @if($ad->subscription)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $ad->subscription->plan->name ?? 'Unknown Plan' }}</p>
                            <p class="text-xs text-gray-400">
                                Expires {{ $ad->subscription->ends_at ? $ad->subscription->ends_at->format('M d, Y') : 'Never' }}
                            </p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-bold
                            {{ $ad->subscription->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($ad->subscription->status) }}
                        </span>
                    </div>
                @else
                    <p class="text-sm text-gray-400">No subscription linked.</p>
                @endif
                <a href="{{ route('vendor.subscriptions.index') }}"
                   class="mt-3 w-full inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700">
                    Manage Subscription <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
