@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.audit-logs.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Audit Log Details</h1>
            <p class="mt-1 text-sm text-gray-500">View detailed information about this activity</p>
        </div>
    </div>

    <!-- Log Details -->
    <div class="log-details bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-2">User</p>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-gray-200 rounded-full text-gray-600 font-semibold">
                            {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : 'S' }}
                        </div>
                        <div>
                            <p class="text-base font-semibold text-gray-900">{{ $log->user ? $log->user->name : 'System' }}</p>
                            <p class="text-sm text-gray-500">{{ $log->user?->email }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-600 mb-2">Action</p>
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-{{ $log->action_color }}-100 text-{{ $log->action_color }}-700 rounded-full font-semibold">
                        <i class="fas {{ $log->action_icon }}"></i>
                        {{ ucfirst($log->action) }}
                    </span>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-600 mb-2">Model Type</p>
                    @if($log->model_type)
                        <span class="px-3 py-1 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg">
                            {{ class_basename($log->model_type) }}
                        </span>
                    @else
                        <span class="text-sm text-gray-400">N/A</span>
                    @endif
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-600 mb-2">Model ID</p>
                    <p class="text-base text-gray-900 font-mono">{{ $log->model_id ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-600 mb-2">IP Address</p>
                    <p class="text-base text-gray-900 font-mono">{{ $log->ip_address ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-600 mb-2">Timestamp</p>
                    <p class="text-base text-gray-900">{{ $log->created_at->format('M d, Y h:i A') }}</p>
                    <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                </div>
            </div>

            <!-- Description -->
            <div>
                <p class="text-sm font-medium text-gray-600 mb-2">Description</p>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-base text-gray-900">{{ $log->description }}</p>
                </div>
            </div>

            <!-- User Agent -->
            @if($log->user_agent)
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-2">User Agent</p>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-700 font-mono break-all">{{ $log->user_agent }}</p>
                    </div>
                </div>
            @endif

            <!-- Changes (if any) -->
            @if($oldValues || $newValues)
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-3">Changes Made</p>
                    <div class="space-y-3">
                        @php
                            $changes = $log->getChangesSummary();
                        @endphp
                        @if(count($changes) > 0)
                            @foreach($changes as $field => $change)
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <p class="text-sm font-semibold text-gray-900 mb-2">{{ ucfirst(str_replace('_', ' ', $field)) }}</p>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs font-medium text-red-600 mb-1">Old Value</p>
                                            <p class="text-sm text-gray-700 bg-red-50 p-2 rounded">
                                                {{ is_array($change['old']) ? json_encode($change['old']) : ($change['old'] ?? 'null') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-green-600 mb-1">New Value</p>
                                            <p class="text-sm text-gray-700 bg-green-50 p-2 rounded">
                                                {{ is_array($change['new']) ? json_encode($change['new']) : ($change['new'] ?? 'null') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500 italic">No changes tracked</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Raw Data (Debug) -->
            @if(config('app.debug'))
                <div>
                    <details class="cursor-pointer">
                        <summary class="text-sm font-medium text-gray-600 mb-2">Raw Data (Debug)</summary>
                        <div class="mt-3 p-4 bg-gray-900 rounded-lg overflow-x-auto">
                            <pre class="text-xs text-green-400 font-mono">{{ json_encode($log->toArray(), JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </details>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-between">
        <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
            <i class="fas fa-arrow-left"></i>Back to Logs
        </a>
    </div>
</div>
@endsection
