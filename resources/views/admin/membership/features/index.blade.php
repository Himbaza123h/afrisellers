@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.memberships.plans.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Plan Features: {{ $membershipPlan->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Manage features and limits for this plan</p>
        </div>
    </div>

    <!-- Plan Info Card -->
    <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ $membershipPlan->name }}</h3>
                <p class="text-sm text-gray-600 mt-1">${{ number_format($membershipPlan->price, 2) }} / {{ $membershipPlan->duration_days }} days</p>
            </div>
            <div class="flex gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $membershipPlan->features()->count() }}</p>
                    <p class="text-xs text-gray-600">Features</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $membershipPlan->subscriptions()->where('status', 'active')->count() }}</p>
                    <p class="text-xs text-gray-600">Active Users</p>
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

    <!-- Add Feature Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Feature</h3>
        <form action="{{ route('admin.memberships.features.store', $membershipPlan) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="md:col-span-1">
                    <label for="feature_key" class="block text-sm font-medium text-gray-700 mb-2">Feature Key <span class="text-red-500">*</span></label>
                    <input type="text" id="feature_key" name="feature_key" placeholder="max_products" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('feature_key') border-red-500 @enderror">
                    @error('feature_key')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-1">
                    <label for="feature_value" class="block text-sm font-medium text-gray-700 mb-2">Feature Value <span class="text-red-500">*</span></label>
                    <input type="text" id="feature_value" name="feature_value" placeholder="50, unlimited, true" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('feature_value') border-red-500 @enderror">
                    @error('feature_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#ff0808] hover:bg-[#e60707] text-white rounded-lg transition-all font-medium">
                        <i class="fas fa-plus"></i>
                        <span>Add Feature</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Common Features Reference -->
        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm font-semibold text-blue-900 mb-2">Common Feature Keys:</p>
            <div class="grid grid-cols-2 gap-2 text-xs text-blue-700 md:grid-cols-4">
                <span><code class="bg-blue-100 px-2 py-1 rounded">max_products</code></span>
                <span><code class="bg-blue-100 px-2 py-1 rounded">max_messages</code></span>
                <span><code class="bg-blue-100 px-2 py-1 rounded">has_rfq_access</code></span>
                <span><code class="bg-blue-100 px-2 py-1 rounded">has_analytics</code></span>
                <span><code class="bg-blue-100 px-2 py-1 rounded">has_ads</code></span>
                <span><code class="bg-blue-100 px-2 py-1 rounded">priority_ranking</code></span>
                <span><code class="bg-blue-100 px-2 py-1 rounded">can_export</code></span>
                <span><code class="bg-blue-100 px-2 py-1 rounded">support_level</code></span>
            </div>
        </div>
    </div>

    <!-- Features List -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Feature Key</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Value</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Added</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($features as $feature)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <code class="text-sm font-mono font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded">{{ $feature->feature_key }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $feature->feature_value }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500">{{ $feature->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="editFeature({{ $feature->id }}, '{{ $feature->feature_key }}', '{{ $feature->feature_value }}')" class="p-2 text-blue-600 rounded-lg hover:bg-blue-50">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.memberships.features.destroy', $feature) }}" method="POST" class="inline" onsubmit="return confirm('Delete this feature?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 rounded-lg hover:bg-red-50">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-list text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-base font-semibold text-gray-900 mb-1">No features added yet</p>
                                    <p class="text-sm text-gray-500">Add features to define what this plan includes</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($features, 'hasPages') && $features->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $features->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Edit Feature Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Feature</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Feature Key</label>
                    <input type="text" id="edit_feature_key" name="feature_key" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Feature Value</label>
                    <input type="text" id="edit_feature_value" name="feature_value" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] font-medium">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function editFeature(id, key, value) {
    document.getElementById('editForm').action = `/admin/memberships/features/${id}`;
    document.getElementById('edit_feature_key').value = key;
    document.getElementById('edit_feature_value').value = value;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection
