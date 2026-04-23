<!-- Filters -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3 mb-4">
    <form method="GET" action="{{ route('admin.addons.index') }}" class="space-y-3">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search addons..." class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm mt-2"></i>
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            <select name="country_id" class="pl-3 pr-8 py-2 border border-gray-300 rounded-lg appearance-none bg-white text-sm">
                <option value="">All Countries</option>
                <option value="global" {{ request('country_id') == 'global' ? 'selected' : '' }}>Global Only</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 text-xs font-medium">
                <i class="fas fa-filter"></i> Apply
            </button>

            @if(request()->hasAny(['search', 'country_id']))
                <a href="{{ route('admin.addons.index') }}" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-xs font-medium">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Location</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Country</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Price</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Subscriptions</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Created</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($addons as $addon)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-900">{{ $addon->locationX }}</span>
                                <span class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $addon->locationY)) }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            @if($addon->country)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $addon->country->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-globe mr-1"></i> Global
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <span class="text-sm font-bold text-gray-900">${{ number_format($addon->price, 2) }}</span>
                            <span class="text-xs text-gray-500">/30 days</span>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-1">
                                <span class="text-sm font-medium text-gray-900">{{ $addon->addonUsers->count() }}</span>
                                <span class="text-xs text-gray-500">
                                    ({{ $addon->activeAddonUsers->count() }} active)
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="text-sm text-gray-900">{{ $addon->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.addons.show', $addon) }}" class="p-1.5 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="View">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('admin.addons.edit', $addon) }}" class="p-1.5 text-gray-600 rounded-lg hover:bg-yellow-50 hover:text-yellow-600" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <form action="{{ route('admin.addons.destroy', $addon) }}" method="POST" class="inline" onsubmit="return confirm('Delete this addon?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-600 rounded-lg hover:bg-red-50 hover:text-red-600" title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-layer-group text-2xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No addons found</p>
                                <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($addons->hasPages())
        <div class="px-4 py-3 border-t">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-700">Showing {{ $addons->firstItem() }}-{{ $addons->lastItem() }} of {{ $addons->total() }}</span>
                <div class="text-sm">{{ $addons->links() }}</div>
            </div>
        </div>
    @endif
</div>
