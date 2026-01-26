<!-- Filters -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3 mb-4">
    <form method="GET" action="{{ route('admin.memberships.plans.index') }}" class="space-y-3">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or slug..." class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            <select name="status" class="pl-3 pr-8 py-2 border border-gray-300 rounded-lg appearance-none bg-white text-sm">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <select name="sort_by" class="pl-3 pr-8 py-2 border border-gray-300 rounded-lg appearance-none bg-white text-sm">
                <option value="display_order" {{ request('sort_by') === 'display_order' ? 'selected' : '' }}>Display Order</option>
                <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name</option>
                <option value="price" {{ request('sort_by') === 'price' ? 'selected' : '' }}>Price</option>
            </select>

            <select name="sort_order" class="pl-3 pr-8 py-2 border border-gray-300 rounded-lg appearance-none bg-white text-sm">
                <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
            </select>

            <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs font-medium">
                <i class="fas fa-filter"></i> Apply
            </button>

            @if(request()->hasAny(['search', 'status', 'sort_by']))
                <a href="{{ route('admin.memberships.plans.index') }}" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-xs font-medium">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Plans Grid -->
<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
    @forelse($plans as $plan)
        <div class="bg-white rounded-lg border-2 {{ $plan->is_active ? 'border-green-200' : 'border-gray-200' }} shadow-sm overflow-hidden transition-all hover:shadow-md">
            <div class="p-4">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ $plan->name }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $plan->slug }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="mb-4">
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-gray-900">${{ number_format($plan->price, 2) }}</span>
                        <span class="text-sm text-gray-500">/ {{ $plan->duration_days }} days</span>
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-xs">
                        <i class="fas fa-users text-purple-600 w-4"></i>
                        <span class="text-gray-700"><strong>{{ $plan->subscriptions_count }}</strong> active subs</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <i class="fas fa-list text-blue-600 w-4"></i>
                        <span class="text-gray-700"><strong>{{ $plan->features_count }}</strong> features</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <i class="fas fa-sort-numeric-up text-green-600 w-4"></i>
                        <span class="text-gray-700">Order: <strong>{{ $plan->display_order }}</strong></span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.memberships.plans.edit', $plan) }}" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-xs font-medium">
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                    </a>
                    <a href="{{ route('admin.memberships.features.index', $plan) }}" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all text-xs font-medium">
                        <i class="fas fa-list"></i>
                        <span>Features</span>
                    </a>
                </div>

                <div class="flex gap-2 mt-2">
                    <form action="{{ route('admin.memberships.plans.toggle-status', $plan) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 {{ $plan->is_active ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded-lg transition-all text-xs font-medium">
                            <i class="fas fa-{{ $plan->is_active ? 'pause' : 'play' }}"></i>
                            <span>{{ $plan->is_active ? 'Deactivate' : 'Activate' }}</span>
                        </button>
                    </form>
                    <form action="{{ route('admin.memberships.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Delete this plan?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-all text-xs font-medium">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full">
            <div class="flex flex-col items-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-crown text-3xl text-gray-300"></i>
                </div>
                <p class="text-base font-semibold text-gray-900 mb-1">No membership plans</p>
                <p class="text-sm text-gray-500 mb-4">Create your first plan to get started</p>
                <a href="{{ route('admin.memberships.plans.create') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] transition-colors text-sm font-medium">
                    <i class="fas fa-plus"></i>
                    <span>Create First Plan</span>
                </a>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if(method_exists($plans, 'hasPages') && $plans->hasPages())
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm px-4 py-3 mt-4">
        <div class="flex items-center justify-between">
            <span class="text-xs text-gray-700">Showing {{ $plans->firstItem() }}-{{ $plans->lastItem() }} of {{ $plans->total() }}</span>
            <div class="text-sm">{{ $plans->links() }}</div>
        </div>
    </div>
@endif
