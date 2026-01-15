@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Membership Plans</h1>
            <p class="mt-1 text-sm text-gray-500">Manage subscription plans and pricing</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.memberships.settings.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="{{ route('admin.memberships.plans.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#ff0808] hover:bg-[#e60707] text-white rounded-lg transition-all font-medium shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Create Plan</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Plans</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-2">
                        <i class="fas fa-crown mr-1 text-[10px]"></i> All plans
                    </span>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-crown text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Active Plans</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                        <i class="fas fa-check-circle mr-1 text-[10px]"></i> Available
                    </span>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Inactive Plans</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['inactive'] }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mt-2">
                        <i class="fas fa-pause-circle mr-1 text-[10px]"></i> Disabled
                    </span>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
                    <i class="fas fa-pause-circle text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Active Subscriptions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_subscriptions'] }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mt-2">
                        <i class="fas fa-users mr-1 text-[10px]"></i> Subscribers
                    </span>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-users text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.memberships.plans.index') }}" class="space-y-4">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or slug..." class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 mt-2"></i>
            </div>

            <div class="flex flex-wrap gap-3 items-center">
                <label class="text-sm font-medium text-gray-700">Filters:</label>

                <select name="status" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <select name="sort_by" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="display_order" {{ request('sort_by') === 'display_order' ? 'selected' : '' }}>Display Order</option>
                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name</option>
                    <option value="price" {{ request('sort_by') === 'price' ? 'selected' : '' }}>Price</option>
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                </select>

                <select name="sort_order" class="pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg appearance-none bg-white">
                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    <i class="fas fa-filter"></i> Apply
                </button>

                @if(request()->hasAny(['search', 'status', 'sort_by']))
                    <a href="{{ route('admin.memberships.plans.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($plans as $plan)
            <div class="bg-white rounded-xl border-2 {{ $plan->is_active ? 'border-green-200' : 'border-gray-200' }} shadow-sm overflow-hidden transition-all hover:shadow-md">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $plan->slug }}</p>
                        </div>
                        <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $plan->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-bold text-gray-900">${{ number_format($plan->price, 2) }}</span>
                            <span class="text-gray-500">/ {{ $plan->duration_days }} days</span>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-users text-purple-600 w-5"></i>
                            <span class="text-gray-700"><strong>{{ $plan->subscriptions_count }}</strong> active subscriptions</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-list text-blue-600 w-5"></i>
                            <span class="text-gray-700"><strong>{{ $plan->features_count }}</strong> features</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-sort-numeric-up text-green-600 w-5"></i>
                            <span class="text-gray-700">Display order: <strong>{{ $plan->display_order }}</strong></span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('admin.memberships.plans.edit', $plan) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium">
                            <i class="fas fa-edit"></i>
                            <span>Edit</span>
                        </a>
                        <a href="{{ route('admin.memberships.features.index', $plan) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all font-medium">
                            <i class="fas fa-list"></i>
                            <span>Features</span>
                        </a>
                    </div>

                    <div class="flex gap-2 mt-3">
                        <form action="{{ route('admin.memberships.plans.toggle-status', $plan) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 {{ $plan->is_active ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded-lg transition-all font-medium">
                                <i class="fas fa-{{ $plan->is_active ? 'pause' : 'play' }}"></i>
                                <span>{{ $plan->is_active ? 'Deactivate' : 'Activate' }}</span>
                            </button>
                        </form>
                        <form action="{{ route('admin.memberships.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-all font-medium">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="flex flex-col items-center py-20">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-crown text-4xl text-gray-300"></i>
                    </div>
                    <p class="text-lg font-semibold text-gray-900 mb-1">No membership plans found</p>
                    <p class="text-sm text-gray-500 mb-6">Create your first membership plan to get started</p>
                    <a href="{{ route('admin.memberships.plans.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] transition-colors font-medium">
                        <i class="fas fa-plus"></i>
                        <span>Create First Plan</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if(method_exists($plans, 'hasPages') && $plans->hasPages())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-4">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-700">Showing {{ $plans->firstItem() }}-{{ $plans->lastItem() }} of {{ $plans->total() }}</span>
                <div>{{ $plans->links() }}</div>
            </div>
        </div>
    @endif
</div>
@endsection
