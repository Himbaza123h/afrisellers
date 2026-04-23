@extends('layouts.home')
@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">My Advertisements</h1>
            <p class="mt-1 text-xs text-gray-500">Manage your ad campaigns across Afrisellers</p>
        </div>
@if($canCreate)
        <a href="{{ route('vendor.advertisements.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#dd0606] font-medium text-sm">
            <i class="fas fa-plus"></i> New Advertisement
        </a>
        @else
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-400 rounded-lg font-medium text-sm cursor-not-allowed"
             title="You have reached your plan's limit of {{ $allowedAds }} ad(s). Upgrade to add more.">
            <i class="fas fa-lock"></i> New Advertisement
            <span class="text-[10px] bg-gray-300 text-gray-500 px-1.5 py-0.5 rounded-full">{{ $allowedAds }}/{{ $allowedAds }}</span>
        </div>
        @endif
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([['Total','total','blue','fa-ad'],['Running','running','green','fa-play-circle'],['Pending','pending','yellow','fa-clock'],['Rejected','rejected','red','fa-times-circle']] as [$label,$key,$color,$icon])
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600 mb-1">{{ $label }}</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats[$key] }}</p>
                </div>
                <div class="w-10 h-10 bg-{{ $color }}-50 rounded-lg flex items-center justify-center">
                    <i class="fas {{ $icon }} text-{{ $color }}-600"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()"><i class="fas fa-times text-green-600"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{!! session('error') !!}</p>
            <button onclick="this.parentElement.remove()"><i class="fas fa-times text-red-600"></i></button>
        </div>
    @endif


    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title..."
                   class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
            <select name="position" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                <option value="">All Positions</option>
                @foreach($positions as $key => $pos)
                    <option value="{{ $key }}" {{ request('position')===$key?'selected':'' }}>{{ $pos['label'] }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                <option value="">All Status</option>
                @foreach(['draft','pending','approved','running','expired','rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-1.5 bg-[#ff0808] text-white rounded-lg text-sm font-medium">Filter</button>
            <a href="{{ route('vendor.advertisements.index') }}" class="px-4 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">Reset</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Ad</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Position</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Type</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Period</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Performance</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($ads as $ad)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($ad->media_url && in_array($ad->type,['image','gif']))
                                    <img src="{{ $ad->media_url }}" class="w-12 h-8 object-cover rounded border border-gray-200">
                                @elseif($ad->type === 'video')
                                    <div class="w-12 h-8 bg-gray-800 rounded border border-gray-200 flex items-center justify-center">
                                        <i class="fas fa-play text-white text-xs"></i>
                                    </div>
                                @elseif($ad->type === 'text')
                                    <div class="w-12 h-8 rounded border border-gray-200 flex items-center justify-center"
                                         style="background:{{ $ad->bg_gradient ?? '#ff0808' }};">
                                        <i class="fas fa-font text-white text-xs"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $ad->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $ad->headline }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium text-gray-700">{{ $positions[$ad->position]['label'] ?? $ad->position }}</span>
                            <p class="text-[10px] text-gray-400">{{ $positions[$ad->position]['size'] ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($ad->type) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-xs text-gray-700">{{ $ad->start_date?->format('M d, Y') ?? '—' }}</p>
                            <p class="text-xs text-gray-500">to {{ $ad->end_date?->format('M d, Y') ?? '—' }}</p>
                            @if($ad->isRunning())
                                <p class="text-[10px] text-green-600 font-medium">{{ $ad->days_remaining }}d left</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-xs text-gray-700"><i class="fas fa-eye mr-1 text-gray-400"></i>{{ number_format($ad->impressions) }}</p>
                            <p class="text-xs text-gray-700"><i class="fas fa-mouse-pointer mr-1 text-gray-400"></i>{{ number_format($ad->clicks) }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $ad->status === 'running'  ? 'bg-green-100 text-green-800'  : '' }}
                                {{ $ad->status === 'pending'  ? 'bg-yellow-100 text-yellow-800': '' }}
                                {{ $ad->status === 'approved' ? 'bg-blue-100 text-blue-800'    : '' }}
                                {{ $ad->status === 'rejected' ? 'bg-red-100 text-red-800'      : '' }}
                                {{ $ad->status === 'expired'  ? 'bg-gray-100 text-gray-800'    : '' }}
                                {{ $ad->status === 'draft'    ? 'bg-gray-100 text-gray-600'    : '' }}">
                                {{ ucfirst($ad->status) }}
                            </span>
                            @if($ad->status === 'rejected' && $ad->rejection_reason)
                                <p class="text-[10px] text-red-500 mt-1">{{ Str::limit($ad->rejection_reason, 30) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('vendor.advertisements.show', $ad) }}" class="text-blue-600 hover:text-blue-800" title="View"><i class="fas fa-eye"></i></a>
                                {{-- edit advertisement --}}
                                {{-- <a href="{{ route('vendor.advertisements.edit', $ad) }}" class="text-gray-600 hover:text-gray-800 }}" title="Edit"><i class="fas fa-edit"></i></a> --}}
                                {{-- @if(in_array($ad->status, ['draft','rejected'])) --}}
                                <a href="{{ route('vendor.advertisements.edit', $ad) }}" class="text-gray-600 hover:text-gray-800" title="Edit"><i class="fas fa-edit"></i></a>
                                {{-- @endif --}}
                                <form action="{{ route('vendor.advertisements.destroy', $ad) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Delete this advertisement?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <i class="fas fa-ad text-4xl text-gray-300 mb-3 block"></i>
                            <p class="text-sm text-gray-500">No advertisements yet</p>
                            <a href="{{ route('vendor.advertisements.create') }}" class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-plus"></i> Create Your First Ad
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ads->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">{{ $ads->links() }}</div>
        @endif
    </div>
</div>
@endsection
