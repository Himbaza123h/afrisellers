<!-- Filters -->
<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 no-print">
    <form method="GET" action="{{ route('admin.product-category.index') }}" class="space-y-4">
        <div class="relative flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by category name or description..." class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            <label class="text-xs font-medium text-gray-700">Filters:</label>

            <select name="type" class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Types</option>
                <option value="main" {{ request('type') == 'main' ? 'selected' : '' }}>Main Categories</option>
                <option value="sub" {{ request('type') == 'sub' ? 'selected' : '' }}>Sub Categories</option>
            </select>

            <select name="status" class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <select name="sort_by" class="pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg appearance-none bg-white">
                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
            </select>

            <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm">
                <i class="fas fa-filter text-xs"></i> Apply
            </button>

            @if(request()->hasAny(['search', 'type', 'status', 'sort_by']))
                <a href="{{ route('admin.product-category.index') }}" class="inline-flex items-center gap-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                    <i class="fas fa-times text-xs"></i> Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left w-10"><input type="checkbox" class="w-4 h-4 rounded"></th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Category Name</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Parent</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Description</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Products</th>
                    <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><input type="checkbox" class="w-4 h-4 rounded"></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br {{ $category->parent_id ? 'from-purple-100 to-purple-200' : 'from-blue-100 to-blue-200' }} rounded-full">
                                    <i class="fas {{ $category->parent_id ? 'fa-folder-tree' : 'fa-folder' }} {{ $category->parent_id ? 'text-purple-700' : 'text-blue-700' }} text-sm"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $category->name }}</span>
                                    @if($category->parent_id)
                                        <span class="text-xs text-gray-500">Sub-category</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($category->parent)
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                    <i class="fas fa-level-up-alt text-[8px]"></i>
                                    {{ $category->parent->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                                    Main Category
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($category->description)
                                <span class="text-xs text-gray-600">{{ Str::limit($category->description, 40) }}</span>
                            @else
                                <span class="text-xs text-gray-400 italic">No description</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $category->products->count() }} {{ Str::plural('product', $category->products->count()) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.product-category.toggle-status', $category) }}" method="POST" class="inline">
                                @csrf
                                @method('POST')
                                <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-medium transition-colors {{ $category->status === 'active' ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                                    {{ ucfirst($category->status) }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.product-category.show', $category) }}" class="p-1.5 text-gray-600 rounded hover:bg-blue-50 hover:text-blue-600" title="View">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.product-category.edit', $category) }}" class="p-1.5 text-gray-600 rounded hover:bg-yellow-50 hover:text-yellow-600" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.product-category.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-600 rounded hover:bg-red-50 hover:text-red-600" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-boxes text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 mb-1">No categories found</p>
                                <p class="text-xs text-gray-500 mb-4">Get started by creating your first category</p>
                                <a href="{{ route('admin.product-category.create') }}" class="inline-flex items-center gap-1 px-3 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium text-sm">
                                    <i class="fas fa-plus text-xs"></i>
                                    <span>Add Category</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(method_exists($categories, 'hasPages') && $categories->hasPages())
        <div class="px-4 py-3 border-t bg-gray-50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-gray-700">
                    Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }}
                </div>
                <div>{{ $categories->links() }}</div>
            </div>
        </div>
    @endif
</div>
