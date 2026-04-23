@extends('layouts.home')
@section('page-content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('vendor.advertisements.index') }}" class="text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $advertisement->title }}</h1>
            <p class="mt-1 text-xs text-gray-500">Advertisement Details</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Preview --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Preview</h3>
            @if($advertisement->media_url && in_array($advertisement->type,['image','gif']))
                <img src="{{ $advertisement->media_url }}" class="w-full rounded border border-gray-200 object-cover" style="max-height:200px;">
            @elseif($advertisement->type === 'video')
                <video src="{{ $advertisement->media_url }}" class="w-full rounded border border-gray-200" controls style="max-height:200px;"></video>
            @elseif($advertisement->type === 'text')
                <div class="w-full h-24 rounded flex items-center justify-center" style="background:{{ $advertisement->bg_gradient ?? '#ff0808' }};">
                    <div class="text-center text-white">
                        @if($advertisement->badge_text)<span class="text-[10px] font-black uppercase px-2 py-0.5 rounded" style="background:{{ $advertisement->accent_color }}; color:#000;">{{ $advertisement->badge_text }}</span>@endif
                        <p class="font-black text-sm mt-1">{{ $advertisement->headline }}</p>
                        <p class="text-xs opacity-75">{{ $advertisement->sub_text }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 space-y-3">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Details</h3>
            @foreach([
                ['Position', \App\Models\Advertisement::positions()[$advertisement->position]['label'] ?? $advertisement->position],
                ['Type', ucfirst($advertisement->type)],
                ['Size', \App\Models\Advertisement::positions()[$advertisement->position]['size'] ?? '—'],
                ['Status', ucfirst($advertisement->status)],
                ['Start Date', $advertisement->start_date?->format('M d, Y') ?? '—'],
                ['End Date', $advertisement->end_date?->format('M d, Y') ?? '—'],
                ['Days Remaining', $advertisement->isRunning() ? $advertisement->days_remaining.' days' : '—'],
                ['Impressions', number_format($advertisement->impressions)],
                ['Clicks', number_format($advertisement->clicks)],
                ['Destination', $advertisement->destination_url ?? '—'],
            ] as [$label, $value])
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500 font-medium">{{ $label }}</span>
                <span class="text-gray-900">{{ $value }}</span>
            </div>
            @endforeach

            @if($advertisement->status === 'rejected' && $advertisement->rejection_reason)
            <div class="p-3 bg-red-50 rounded-lg border border-red-200 mt-3">
                <p class="text-xs font-semibold text-red-700">Rejection Reason</p>
                <p class="text-xs text-red-600 mt-1">{{ $advertisement->rejection_reason }}</p>
            </div>
            @endif
        </div>
    </div>

    @if(in_array($advertisement->status, ['draft','rejected']))
    <div class="flex gap-3">
        <a href="{{ route('vendor.advertisements.edit', $advertisement) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg text-sm font-medium">
            <i class="fas fa-edit"></i> Edit & Resubmit
        </a>
    </div>
    @endif

</div>
@endsection
