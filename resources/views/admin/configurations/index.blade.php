@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">System Configurations</h1>
            <p class="mt-1 text-xs text-gray-500">Manage system-wide settings and configurations</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.configurations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] font-medium text-sm">
                <i class="fas fa-plus text-xs"></i>
                <span>Add Configuration</span>
            </a>
            <a href="{{ route('admin.configurations.print') }}?{{ http_build_query(request()->all()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                <i class="fas fa-print text-xs"></i>
                <span>Print</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Configurations</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 to-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cog text-lg text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Active</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-50 to-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-lg text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600 mb-1">Inactive</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['inactive'] }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-50 to-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pause-circle text-lg text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.configurations.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID or value..."
                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                </div>

                <!-- Type Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="string"  {{ request('type') === 'string'  ? 'selected' : '' }}>String</option>
                        <option value="integer" {{ request('type') === 'integer' ? 'selected' : '' }}>Integer</option>
                        <option value="boolean" {{ request('type') === 'boolean' ? 'selected' : '' }}>Boolean</option>
                        <option value="array"   {{ request('type') === 'array'   ? 'selected' : '' }}>Array</option>
                        <option value="json"    {{ request('type') === 'json'    ? 'selected' : '' }}>JSON</option>
                        <option value="text"    {{ request('type') === 'text'    ? 'selected' : '' }}>Text</option>
                        <option value="file"    {{ request('type') === 'file'    ? 'selected' : '' }}>File</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Country Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Country</label>
                    <select name="country_id" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                        <option value="">All Countries</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort_by" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
                        <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                        <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>Last Updated</option>
                        <option value="unique_id"  {{ request('sort_by') === 'unique_id'  ? 'selected' : '' }}>Unique ID</option>
                        <option value="type"       {{ request('sort_by') === 'type'       ? 'selected' : '' }}>Type</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-1.5 bg-[#ff0808] text-white rounded-lg hover:bg-[#e60707] font-medium text-sm">
                    <i class="fas fa-filter mr-1"></i> Apply Filters
                </button>
                <a href="{{ route('admin.configurations.index') }}" class="px-4 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">
                    <i class="fas fa-redo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Title/ID</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Type</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Value</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Country</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold text-left text-gray-700 uppercase">Updated</th>
                        <th class="px-4 py-3 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($configurations as $config)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $config->unique_id }}</span>
                                    <span class="text-xs text-gray-500">ID: {{ $config->id }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $config->type === 'string'  ? 'bg-blue-100 text-blue-800'   : '' }}
                                    {{ $config->type === 'integer' ? 'bg-purple-100 text-purple-800': '' }}
                                    {{ $config->type === 'boolean' ? 'bg-green-100 text-green-800'  : '' }}
                                    {{ $config->type === 'array'   ? 'bg-orange-100 text-orange-800': '' }}
                                    {{ $config->type === 'json'    ? 'bg-pink-100 text-pink-800'    : '' }}
                                    {{ $config->type === 'text'    ? 'bg-gray-100 text-gray-800'    : '' }}
                                    {{ $config->type === 'file'    ? 'bg-indigo-100 text-indigo-800': '' }}">
                                    {{ ucfirst($config->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="max-w-xs">
                                    @if(in_array($config->type, ['array', 'json']))
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ is_array($config->value) ? json_encode($config->value) : $config->value }}</code>
                                    @elseif($config->type === 'boolean')
                                        <span class="text-sm {{ $config->value ? 'text-green-600' : 'text-red-600' }} font-medium">
                                            {{ $config->value ? 'True' : 'False' }}
                                        </span>
                                    @elseif($config->type === 'file')
                                        @php $files = $config->files ?? []; @endphp
                                        @if(count($files) > 0)
                                            <div class="flex gap-1 flex-wrap">
                                                @foreach(array_slice($files, 0, 3) as $file)
                                                    @php $ext = strtolower(pathinfo($file['path'], PATHINFO_EXTENSION)); @endphp
                                                    @if(in_array($ext, ['jpg','jpeg','png','gif','webp','svg']))
                                                        <a href="{{ $file['url'] }}" target="_blank">
                                                            <img src="{{ $file['url'] }}" alt="file"
                                                                 class="h-9 w-9 rounded border border-gray-200 object-cover hover:opacity-80 transition">
                                                        </a>
                                                    @else
                                                        <a href="{{ $file['url'] }}" target="_blank"
                                                           class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs">
                                                            <i class="fas fa-file text-xs"></i>
                                                            {{ Str::limit($file['name'], 15) }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                                @if(count($files) > 3)
                                                    <span class="text-xs text-gray-400 self-center">+{{ count($files) - 3 }} more</span>
                                                @endif
                                            </div>
                                        @elseif($config->value)
                                            @php
                                                $fileUrl = Storage::disk('public')->url($config->value);
                                                $ext = strtolower(pathinfo($config->value, PATHINFO_EXTENSION));
                                            @endphp
                                            @if(in_array($ext, ['jpg','jpeg','png','gif','webp','svg']))
                                                <a href="{{ $fileUrl }}" target="_blank">
                                                    <img src="{{ $fileUrl }}" alt="file"
                                                         class="h-10 w-auto rounded border border-gray-200 object-cover hover:opacity-80 transition">
                                                </a>
                                            @else
                                                <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm">
                                                    <i class="fas fa-file text-xs"></i>
                                                    {{ Str::limit(basename($config->value), 30) }}
                                                </a>
                                            @endif
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-900">{{ Str::limit($config->value, 50) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($config->country_id)
                                    <span class="text-xs text-gray-700 font-medium">
                                        {{ optional($config->country)->name ?? '—' }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Global</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $config->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $config->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs text-gray-600">
                                    <div>{{ $config->updated_at->format('M d, Y') }}</div>
                                    <div class="text-gray-500">{{ $config->updated_at->format('h:i A') }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.configurations.edit', $config) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.configurations.toggle-status', $config) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="{{ $config->is_active ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800' }}"
                                                title="{{ $config->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $config->is_active ? 'pause' : 'play' }}-circle"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.configurations.destroy', $config) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this configuration?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500 text-sm">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                                No configurations found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($configurations->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $configurations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
