@extends('layouts.home')

@section('page-content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between no-print">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('regional.agents.index') }}"
                    class="text-gray-500 hover:text-gray-900 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Agent Details</h1>
            </div>
            <p class="text-sm text-gray-500 ml-7">
                {{ $agent->name }}
                <span class="mx-1 text-gray-300">|</span>
                <span class="text-indigo-600 font-medium">{{ $region->name }}</span>
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if($agent->status === 'active')
                <form action="{{ route('regional.agents.suspend', $agent->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to suspend this agent?')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm">
                        <i class="fas fa-ban"></i> Suspend Account
                    </button>
                </form>
            @else
                <form action="{{ route('regional.agents.activate', $agent->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium shadow-sm">
                        <i class="fas fa-check-circle"></i> Activate Account
                    </button>
                </form>
            @endif
            <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Status Summary Bar --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Agent ID</p>
                <p class="text-lg font-bold text-gray-900">#{{ $agent->id }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Registered</p>
                <p class="text-sm font-semibold text-gray-900">{{ $agent->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-gray-500">{{ $agent->created_at->format('h:i A') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Country</p>
                <p class="text-sm font-semibold text-gray-900">{{ $agent->country->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Region</p>
                <p class="text-sm font-semibold text-indigo-700">{{ $region->name }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Account Status</p>
                @php
                    $map = [
                        'active'    => ['Active',    'bg-green-100 text-green-800'],
                        'suspended' => ['Suspended', 'bg-red-100 text-red-800'],
                    ];
                    [$label, $cls] = $map[$agent->status] ?? ['Inactive', 'bg-gray-100 text-gray-700'];
                @endphp
                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-medium {{ $cls }}">
                    {{ $label }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Agent Profile --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                <div class="px-6 pb-6">
                    <div class="flex items-end -mt-10 mb-4">
                        <div class="w-20 h-20 rounded-xl border-4 border-white shadow-lg bg-indigo-100 flex items-center justify-center">
                            <span class="text-3xl font-bold text-indigo-700">
                                {{ strtoupper(substr($agent->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="ml-4 mb-1">
                            <h2 class="text-xl font-bold text-gray-900">{{ $agent->name }}</h2>
                            <p class="text-sm text-gray-500">Agent &bull; {{ $agent->country->name ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 mb-1">Full Name</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $agent->name }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 mb-1">Email Address</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $agent->email }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 mb-1">Country</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $agent->country->name ?? '—' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 mb-1">Member Since</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $agent->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Agent Settings --}}
            @if($agent->agentSettings)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Agent Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($agent->agentSettings->toArray() as $key => $value)
                            @if(!in_array($key, ['id','user_id','created_at','updated_at']) && $value !== null)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-500 mb-1 capitalize">
                                        {{ str_replace('_', ' ', $key) }}
                                    </p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        @if(is_bool($value))
                                            {{ $value ? 'Yes' : 'No' }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Product Activity --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Activity</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="p-4 bg-blue-50 rounded-lg text-center">
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($stats['total_products']) }}</p>
                        <p class="text-xs font-medium text-blue-600 mt-1">Total Products</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg text-center">
                        <p class="text-2xl font-bold text-green-900">{{ number_format($stats['active_products']) }}</p>
                        <p class="text-xs font-medium text-green-600 mt-1">Active Products</p>
                    </div>
                    <div class="p-4 bg-indigo-50 rounded-lg text-center">
                        <p class="text-2xl font-bold text-indigo-900">{{ number_format($stats['total_views']) }}</p>
                        <p class="text-xs font-medium text-indigo-600 mt-1">Total Views</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Roles --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Roles</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($agent->roles as $role)
                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                            {{ $role->name }}
                        </span>
                    @empty
                        <p class="text-sm text-gray-400">No roles assigned</p>
                    @endforelse
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 no-print">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    @if($agent->status === 'active')
                        <form action="{{ route('regional.agents.suspend', $agent->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Are you sure you want to suspend this agent?')"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-sm">
                                <i class="fas fa-ban mr-2"></i> Suspend Account
                            </button>
                        </form>
                    @else
                        <form action="{{ route('regional.agents.activate', $agent->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm">
                                <i class="fas fa-check-circle mr-2"></i> Activate Account
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('regional.agents.index') }}"
                        class="flex items-center justify-center gap-2 w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm">
                        <i class="fas fa-arrow-left"></i> Back to Agents
                    </a>
                </div>
            </div>

            {{-- Account Timeline --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Timeline</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-user-plus text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Account Created</p>
                            <p class="text-xs text-gray-500">{{ $agent->created_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                    @if($agent->updated_at && $agent->updated_at != $agent->created_at)
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-full bg-yellow-100 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fas fa-pen text-yellow-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Last Updated</p>
                                <p class="text-xs text-gray-500">{{ $agent->updated_at->format('M d, Y \a\t h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
    }
</style>
@endsection
