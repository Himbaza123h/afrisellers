@extends('layouts.home')

@section('page-content')
<!-- Header Section -->
<div class="mb-4 sm:mb-6 lg:mb-8">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.country.index') }}" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-lg font-black text-gray-900 uppercase">Country Details</h1>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">View country information</p>
        </div>
    </div>
</div>

<!-- Country Details Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Flag Preview -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-3">Flag</label>
            <div class="border border-gray-300 rounded-lg p-6 bg-gray-50 inline-block">
                @if($country->flag_url)
                    <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" class="w-24 h-18 object-cover rounded border border-gray-300" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'96\' height=\'72\'%3E%3Crect fill=\'%23e5e7eb\' width=\'96\' height=\'72\'/%3E%3C/svg%3E'">
                @else
                    <div class="w-24 h-18 bg-gray-200 rounded border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-flag text-gray-400 text-2xl"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- Country Information -->
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Country Name</label>
                <p class="text-lg font-bold text-gray-900">{{ $country->name }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $country->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($country->status) }}
                </span>
            </div>

            @if($country->flag_url)
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Flag URL</label>
                    <a href="{{ $country->flag_url }}" target="_blank" class="text-sm text-blue-600 hover:underline break-all">
                        {{ $country->flag_url }}
                    </a>
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Created At</label>
                <p class="text-sm text-gray-700">{{ $country->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Last Updated</label>
                <p class="text-sm text-gray-700">{{ $country->updated_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center gap-3 pt-6 mt-6 border-t border-gray-200">
        <a href="{{ route('admin.country.edit', $country) }}" class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-semibold shadow-md">
            <i class="fas fa-edit mr-2"></i>
            Edit Country
        </a>
        <form action="{{ route('admin.country.toggle-status', $country) }}" method="POST" class="inline">
            @csrf
            @method('POST')
            <button type="submit" class="px-6 py-3 text-white rounded-lg transition-colors font-semibold shadow-md {{ $country->status === 'active' ? 'bg-gray-600 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }}">
                <i class="fas fa-{{ $country->status === 'active' ? 'pause' : 'play' }} mr-2"></i>
                {{ $country->status === 'active' ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
        <form action="{{ route('admin.country.destroy', $country) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this country? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-md">
                <i class="fas fa-trash mr-2"></i>
                Delete Country
            </button>
        </form>
        <a href="{{ route('admin.country.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold ml-auto">
            Back to List
        </a>
    </div>
</div>
@endsection

