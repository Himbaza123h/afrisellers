@extends('layouts.home')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.tradeshows.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tradeshow Details</h1>
                <p class="text-sm text-gray-500 mt-1">View complete information about this tradeshow</p>
            </div>
        </div>
        <div class="flex gap-3">
            @if($tradeshow->status === 'pending')
                <form action="{{ route('admin.tradeshows.approve', $tradeshow) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                        <i class="fas fa-check mr-2"></i> Approve
                    </button>
                </form>
            @endif
            @if(!$tradeshow->is_verified)
                <form action="{{ route('admin.tradeshows.verify', $tradeshow) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">
                        <i class="fas fa-certificate mr-2"></i> Verify
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.tradeshows.feature', $tradeshow) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-amber-600 text-white rounded-lg hover:bg-amber-700 font-medium">
                    <i class="fas fa-star mr-2"></i> {{ $tradeshow->is_featured ? 'Unfeature' : 'Feature' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Status Badges -->
    <div class="flex flex-wrap gap-3">
        <span class="px-4 py-2 rounded-full text-sm font-medium {{ $tradeshow->status === 'published' ? 'bg-green-100 text-green-800' : ($tradeshow->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
            {{ ucfirst($tradeshow->status) }}
        </span>
        @if($tradeshow->is_verified)
            <span class="px-4 py-2 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                <i class="fas fa-certificate mr-1"></i> Verified
            </span>
        @endif
        @if($tradeshow->is_featured)
            <span class="px-4 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                <i class="fas fa-star mr-1"></i> Featured
            </span>
        @endif
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tradeshow Name</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $tradeshow->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tradeshow Number</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $tradeshow->tradeshow_number }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Industry</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $tradeshow->industry ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Category</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $tradeshow->category ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Description</h2>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $tradeshow->description ?? 'No description available' }}</p>
            </div>

            <!-- Location & Venue -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Location & Venue</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Venue Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $tradeshow->venue_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">City</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $tradeshow->city }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Country</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $tradeshow->country->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $tradeshow->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Organizer Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Organizer Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Organizer Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $tradeshow->organizer_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Contact Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $tradeshow->organizer_email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Contact Phone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $tradeshow->organizer_phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Website</label>
                        @if($tradeshow->website)
                            <a href="{{ $tradeshow->website }}" target="_blank" class="mt-1 text-sm text-blue-600 hover:underline">{{ $tradeshow->website }}</a>
                        @else
                            <p class="mt-1 text-sm text-gray-900">N/A</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Event Timeline -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Event Timeline</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Start Date</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $tradeshow->start_date->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">End Date</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $tradeshow->end_date->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Duration</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $tradeshow->duration_days }} days</p>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Statistics</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Expected Visitors</label>
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ number_format($tradeshow->expected_visitors) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Exhibition Area</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $tradeshow->exhibition_area ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Number of Exhibitors</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $tradeshow->exhibitors_count ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Created By -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Created By</h2>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">{{ substr($tradeshow->user->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $tradeshow->user->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $tradeshow->user->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="pt-3 border-t">
                        <p class="text-xs text-gray-500">Created: {{ $tradeshow->created_at->format('M d, Y h:i A') }}</p>
                        <p class="text-xs text-gray-500">Updated: {{ $tradeshow->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Actions</h2>
                <div class="space-y-2">
                    @if($tradeshow->status === 'published')
                        <form action="{{ route('admin.tradeshows.suspend', $tradeshow) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium" onclick="return confirm('Suspend this tradeshow?')">
                                <i class="fas fa-ban mr-2"></i> Suspend
                            </button>
                        </form>
                    @endif
                    @if($tradeshow->is_verified)
                        <form action="{{ route('admin.tradeshows.unverify', $tradeshow) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium">
                                <i class="fas fa-times-circle mr-2"></i> Revoke Verification
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.tradeshows.destroy', $tradeshow) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium" onclick="return confirm('Are you sure you want to delete this tradeshow?')">
                            <i class="fas fa-trash mr-2"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
