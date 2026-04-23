@extends('layouts.home')

@section('page-content')
<div class="space-y-6">

    {{-- ── Page Header ──────────────────────────────────────────── --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Advertisements & Promotions</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your promotional media to boost visibility</p>
        </div>
        @if($canAds)
            <a href="{{ route('vendor.ads.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition-all shadow-sm">
                <i class="fas fa-plus"></i> Create New Ad
            </a>
        @endif
    </div>

    {{-- ── Subscription Lock Banner ─────────────────────────────── --}}
    @if(!$canAds)
    <div class="relative overflow-hidden bg-blue-950 rounded-2xl p-8 text-white shadow-xl">
        <div class="absolute inset-0 opacity-10"
             style="background-image: repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%);background-size:18px 18px;"></div>
        <div class="relative z-10 flex flex-col items-center text-center max-w-lg mx-auto">
            <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-4 border border-white/20">
                <i class="fas fa-bullhorn text-3xl text-blue-300"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Unlock Advertisements</h2>
            <p class="text-blue-200 text-sm mb-6 leading-relaxed">
                Your current plan doesn't include the Ads & Promotions feature.
                Upgrade to start running image and video ads across the platform.
            </p>
            <div class="flex flex-wrap justify-center gap-2 mb-6">
                @foreach(['Image Ads','Video Ads','Homepage Placement','Sidebar Placement','Banner Ads','Feed Promotion', 'Articles'] as $f)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/10 border border-white/20 rounded-full text-xs font-medium text-blue-100">
                        <i class="fas fa-lock text-[9px]"></i> {{ $f }}
                    </span>
                @endforeach
            </div>
            <a href="{{ route('vendor.subscriptions.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-400 text-white rounded-xl font-bold text-sm transition-all shadow-lg">
                <i class="fas fa-arrow-circle-up"></i> Upgrade Your Plan
            </a>
        </div>
    </div>
    @endif

    {{-- ── Flash Messages ───────────────────────────────────────── --}}
    @if(session('success'))
    <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
        <i class="fas fa-check-circle mt-0.5 text-green-500 flex-shrink-0"></i>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <i class="fas fa-exclamation-circle mt-0.5 text-red-500 flex-shrink-0"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Stats Cards ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label'=>'Total Ads',    'value'=>$stats['total'],       'icon'=>'photo-video',    'color'=>'blue'],
            ['label'=>'Active Ads',   'value'=>$stats['active'],      'icon'=>'play-circle',    'color'=>'green'],
            ['label'=>'Impressions',  'value'=>number_format($stats['impressions']), 'icon'=>'eye', 'color'=>'purple'],
            ['label'=>'Total Clicks', 'value'=>number_format($stats['clicks']),    'icon'=>'mouse-pointer','color'=>'orange'],
        ] as $s)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-{{ $s['color'] }}-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-{{ $s['icon'] }} text-xl text-{{ $s['color'] }}-600"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">{{ $s['label'] }}</p>
                <p class="text-xl font-bold text-gray-900">{{ $s['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Filters ──────────────────────────────────────────────── --}}
    @if($canAds)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('vendor.ads.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs mt-2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ads..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                @foreach(['draft','active','paused','expired','rejected'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <select name="media_type" class="px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Types</option>
                <option value="image" @selected(request('media_type') === 'image')>Images</option>
                <option value="video" @selected(request('media_type') === 'video')>Videos</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-all">
                Filter
            </button>
            @if(request()->hasAny(['status','media_type','search']))
                <a href="{{ route('vendor.ads.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-all">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            @endif
        </form>
    </div>

    {{-- ── Ads Grid ─────────────────────────────────────────────── --}}
    @if($ads->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($ads as $ad)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col group hover:shadow-md transition-all">

            {{-- Media Preview --}}
            <div class="relative h-44 bg-gray-100 overflow-hidden">
                @if($ad->media_type === 'image')
                    <img src="{{ asset('storage/' . $ad->media_path) }}"
                         alt="{{ $ad->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <video class="w-full h-full object-cover" muted preload="metadata">
                        <source src="{{ asset('storage/' . $ad->media_path) }}" type="video/mp4">
                    </video>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-12 h-12 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center">
                            <i class="fas fa-play text-white text-sm ml-0.5"></i>
                        </div>
                    </div>
                @endif

                {{-- Type Badge --}}
                <div class="absolute top-3 left-3">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold
                        {{ $ad->media_type === 'video' ? 'bg-purple-600 text-white' : 'bg-blue-600 text-white' }}">
                        <i class="fas fa-{{ $ad->media_type === 'video' ? 'video' : 'image' }} text-[9px]"></i>
                        {{ strtoupper($ad->media_type) }}
                    </span>
                </div>

                {{-- Status Badge --}}
                <div class="absolute top-3 right-3">
                    @php
                        $statusMap = [
                            'active'   => ['bg-green-500',  'Active'],
                            'draft'    => ['bg-gray-500',   'Draft'],
                            'paused'   => ['bg-orange-500', 'Paused'],
                            'expired'  => ['bg-red-500',    'Expired'],
                            'rejected' => ['bg-red-700',    'Rejected'],
                        ];
                        [$sBg, $sLabel] = $statusMap[$ad->status] ?? ['bg-gray-500', ucfirst($ad->status)];
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 {{ $sBg }} text-white rounded-full text-xs font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-white/70 animate-pulse"></span> {{ $sLabel }}
                    </span>
                </div>
            </div>

            {{-- Info --}}
            <div class="p-5 flex flex-col flex-1">
                <h3 class="font-bold text-gray-900 text-sm mb-1 truncate">{{ $ad->title }}</h3>
                <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $ad->description ?? '—' }}</p>

                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex items-center gap-1 text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-lg">
                        <i class="fas fa-map-marker-alt text-[9px]"></i> {{ ucfirst($ad->placement) }}
                    </span>
                    <span class="text-xs text-gray-400">{{ $ad->media_size_formatted }}</span>
                </div>

                {{-- Stats row --}}
                <div class="flex items-center gap-4 text-xs text-gray-500 mb-4 pb-4 border-b border-gray-100">
                    <span><i class="fas fa-eye mr-1 text-gray-400"></i>{{ number_format($ad->impressions) }}</span>
                    <span><i class="fas fa-mouse-pointer mr-1 text-gray-400"></i>{{ number_format($ad->clicks) }}</span>
                    <span class="ml-auto font-semibold text-blue-600">{{ $ad->ctr }}% CTR</span>
                </div>

                {{-- Sub validity warning --}}
                @if(!$ad->subscription || $ad->subscription->status !== 'active')
                <div class="flex items-center gap-2 mb-3 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Subscription expired — ad inactive</span>
                </div>
                @endif

                {{-- Actions --}}
                <div class="flex items-center gap-2 mt-auto">
                    <a href="{{ route('vendor.ads.show', $ad) }}"
                       class="flex-1 text-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-200 transition-all">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    <a href="{{ route('vendor.ads.edit', $ad) }}"
                       class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-all">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <form action="{{ route('vendor.ads.toggle-status', $ad) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="px-3 py-2 rounded-lg text-xs font-semibold transition-all
                            {{ $ad->status === 'active' ? 'bg-orange-50 text-orange-700 hover:bg-orange-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                            <i class="fas fa-{{ $ad->status === 'active' ? 'pause' : 'play' }}"></i>
                        </button>
                    </form>
                    <form action="{{ route('vendor.ads.destroy', $ad) }}" method="POST"
                          onsubmit="return confirm('Delete this ad permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-red-50 text-red-600 rounded-lg text-xs font-semibold hover:bg-red-100 transition-all">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $ads->links() }}
    </div>

    @else
    {{-- Empty state --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-16 text-center">
        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-bullhorn text-3xl text-blue-400"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">No Ads Yet</h3>
        <p class="text-sm text-gray-500 mb-6">Create your first ad to start promoting your products on the platform.</p>
        <a href="{{ route('vendor.ads.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all shadow-md">
            <i class="fas fa-plus"></i> Create Your First Ad
        </a>
    </div>
    @endif
    @endif

</div>
@endsection
