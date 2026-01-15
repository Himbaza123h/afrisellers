@extends('layouts.home')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>
@endpush

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Showrooms</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your physical showroom locations and displays</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <a href="{{ route('vendor.showrooms.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#ff0808] text-white rounded-lg hover:bg-red-700 transition-all font-medium shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Add New Showroom</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Showrooms</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $showrooms->total() }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-building mr-1 text-[10px]"></i> Active
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                    <i class="fas fa-building text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $showrooms->sum('products_count') }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-box mr-1 text-[10px]"></i> In showrooms
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                    <i class="fas fa-box text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Views</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($showrooms->sum('views_count')) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-eye mr-1 text-[10px]"></i> All time
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                    <i class="fas fa-eye text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Inquiries</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($showrooms->sum('inquiries_count')) }}</p>
                    <div class="mt-3 flex items-center gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            <i class="fas fa-envelope mr-1 text-[10px]"></i> Received
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl">
                    <i class="fas fa-envelope text-2xl text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
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

    <!-- Table -->
    <div class="overflow-hidden bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Showroom</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Location</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Business Type</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Products</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Views</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Inquiries</th>
                        <th class="px-6 py-4 text-xs font-semibold text-left text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-center text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($showrooms as $showroom)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0">
                                        @if($showroom->primary_image)
                                            <img src="{{ $showroom->primary_image }}" alt="{{ $showroom->name }}" class="w-16 h-16 rounded-lg object-cover border-2 border-gray-200">
                                        @else
                                            <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center border-2 border-gray-200">
                                                <i class="fas fa-building text-gray-400 text-xl"></i>
                                            </div>
                                        @endif
                                        @if($showroom->is_featured)
                                            <div class="absolute -top-1 -right-1">
                                                <span class="inline-flex items-center justify-center w-5 h-5 bg-yellow-400 rounded-full">
                                                    <i class="fas fa-star text-white text-xs"></i>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">{{ $showroom->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $showroom->showroom_number }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">{{ $showroom->city }}</span>
                                    <span class="text-xs text-gray-500">{{ $showroom->country->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($showroom->business_type)
                                    <span class="text-sm text-gray-700">{{ ucfirst($showroom->business_type) }}</span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $showroom->products_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ number_format($showroom->views_count ?? 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $showroom->inquiries_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($showroom->status === 'active')
                                        <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800 inline-flex items-center w-fit">
                                            <i class="fas fa-check-circle mr-1"></i> Active
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 inline-flex items-center w-fit">
                                            <i class="fas fa-pause-circle mr-1"></i> Inactive
                                        </span>
                                    @endif
                                    @if($showroom->is_verified)
                                        <span class="text-xs text-blue-600 flex items-center gap-1">
                                            <i class="fas fa-shield-check"></i> Verified
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('vendor.showrooms.show', $showroom->id) }}" class="p-2 text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-600" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('vendor.showrooms.products', $showroom->id) }}" class="p-2 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600" title="Manage Products">
                                        <i class="fas fa-box"></i>
                                    </a>
                                    <a href="{{ route('vendor.showrooms.edit', $showroom->id) }}" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-800" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gradient-to-br from-purple-50 to-purple-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-building text-4xl text-purple-600"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No showrooms yet</p>
                                    <p class="text-sm text-gray-500 mb-6">Create your first showroom to showcase your products</p>
                                    <a href="{{ route('vendor.showrooms.create') }}"
                                       class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff0808] text-white font-semibold rounded-lg hover:bg-red-700 transition-all shadow-sm">
                                        <i class="fas fa-plus"></i>
                                        <span>Create Your First Showroom</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($showrooms, 'hasPages') && $showrooms->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700">Showing {{ $showrooms->firstItem() }}-{{ $showrooms->lastItem() }} of {{ $showrooms->total() }}</span>
                    <div>{{ $showrooms->links() }}</div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
