@extends('layouts.home')

@section('page-content')
<div class="space-y-5 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.system-logs.index') }}"
           class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Log Entry #{{ $log->id }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ $log->created_at->format('M d, Y \a\t H:i:s') }}</p>
        </div>
    </div>

    {{-- Level Banner --}}
    @php
        $banners = [
            'critical' => 'bg-red-50 border-red-200 text-red-800',
            'error'    => 'bg-orange-50 border-orange-200 text-orange-800',
            'warning'  => 'bg-amber-50 border-amber-200 text-amber-800',
            'info'     => 'bg-blue-50 border-blue-200 text-blue-800',
        ];
        $bannerCls = $banners[$log->level] ?? $banners['info'];
    @endphp
    <div class="p-4 rounded-lg border {{ $bannerCls }} flex items-center gap-3">
        <i class="fas {{ $log->icon }} text-xl flex-shrink-0"></i>
        <div>
            <p class="text-sm font-bold">{{ strtoupper($log->level) }} — {{ ucfirst($log->action ?? 'event') }}</p>
            <p class="text-sm mt-0.5">{{ $log->description }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Details --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Log Details</h3>
            <dl class="space-y-3">
                @foreach([
                    ['ID',          $log->id],
                    ['Level',       strtoupper($log->level)],
                    ['Module',      $log->module ?? '—'],
                    ['Action',      $log->action ?? '—'],
                    ['Entity Type', $log->entity_type ?? '—'],
                    ['Entity ID',   $log->entity_id ?? '—'],
                    ['IP Address',  $log->ip_address ?? '—'],
                    ['Time',        $log->created_at->format('M d, Y H:i:s')],
                ] as [$label, $value])
                <div class="flex items-start justify-between gap-2">
                    <dt class="text-xs text-gray-400 flex-shrink-0">{{ $label }}</dt>
                    <dd class="text-xs font-semibold text-gray-700 text-right break-all">{{ $value }}</dd>
                </div>
                @endforeach
            </dl>
        </div>

        {{-- User Info --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">User</h3>
            @if($log->user)
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-500 to-blue-700 flex items-center justify-center">
                        <span class="text-white text-lg font-bold">
                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $log->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $log->user->email }}</p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-robot text-gray-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-600">System</p>
                        <p class="text-xs text-gray-400">Automated action</p>
                    </div>
                </div>
            @endif

            @if($log->user_agent)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <dt class="text-xs text-gray-400 mb-1">User Agent</dt>
                    <dd class="text-[10px] text-gray-500 break-all leading-relaxed">{{ $log->user_agent }}</dd>
                </div>
            @endif
        </div>
    </div>

    {{-- Metadata --}}
    @if($log->metadata)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Metadata</h3>
            <pre class="text-xs text-gray-700 bg-gray-50 rounded-lg p-4 overflow-x-auto leading-relaxed">{{ json_encode($log->metadata, JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif
</div>
@endsection
