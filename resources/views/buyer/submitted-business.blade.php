@extends('layouts.app')

@section('title', 'Submitted Business Application')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Business Application Status</h1>
                        <p class="mt-1 text-sm text-gray-600">View your submitted vendor application details</p>
                    </div>
                    <a href="{{ route('buyer.dashboard.home') }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Status Alert -->
            @if ($businessProfile->verification_status === 'pending')
                <div class="p-4 mb-6 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex gap-3 items-start">
                        <i class="text-xl text-yellow-600 fas fa-clock mt-0.5"></i>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-yellow-900">Application Pending Review</h3>
                            <p class="mt-1 text-sm text-yellow-700">
                                Your business application has been submitted and is currently under review by our team.
                                We will notify you once the review is complete.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif ($businessProfile->verification_status === 'verified')
                <div class="p-4 mb-6 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex gap-3 items-start">
                        <i class="text-xl text-green-600 fas fa-check-circle mt-0.5"></i>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-green-900">Application Verified</h3>
                            <p class="mt-1 text-sm text-green-700">
                                Your business application has been verified. You can now access your vendor dashboard.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif ($businessProfile->verification_status === 'rejected')
                <div class="p-4 mb-6 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex gap-3 items-start">
                        <i class="text-xl text-red-600 fas fa-times-circle mt-0.5"></i>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-red-900">Application Rejected</h3>
                            <p class="mt-1 text-sm text-red-700">
                                Your business application has been rejected. Please contact support for more information.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Business Information Card -->
            <div class="mb-6 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Business Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Business Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->business_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Registration Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->business_registration_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->phone_code }} {{ $businessProfile->phone }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Country</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->country->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">City</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->city }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Submitted Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Owner Information Card -->
            @if ($ownerID)
                <div class="mb-6 bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900">Owner Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">ID/Passport Number</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $ownerID->id_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Owner Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->user->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Documents Card -->
            <div class="mb-6 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">Submitted Documents</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Business Registration Document -->
                        @if ($businessProfile->business_registration_doc)
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Business Registration Document</label>
                                <a href="{{ Storage::url($businessProfile->business_registration_doc) }}" target="_blank"
                                    class="inline-flex gap-2 items-center px-4 py-2 text-sm font-medium text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                                    <i class="fas fa-file-pdf"></i>
                                    View Document
                                </a>
                            </div>
                        @endif

                        <!-- Owner ID Document -->
                        @if ($ownerID && $ownerID->id_document_path)
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Owner ID Document</label>
                                <a href="{{ Storage::url($ownerID->id_document_path) }}" target="_blank"
                                    class="inline-flex gap-2 items-center px-4 py-2 text-sm font-medium text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                                    <i class="fas fa-file-pdf"></i>
                                    View Document
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 justify-end">
                @php
                    $hasVendor = \App\Models\Vendor\Vendor::where('user_id', $businessProfile->user_id)->exists();
                @endphp
                @if ($businessProfile->verification_status === 'verified' && $hasVendor)
                    <a href="{{ route('vendor.dashboard.home') }}"
                        class="px-6 py-3 text-sm font-medium text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                        Go to Vendor Dashboard
                    </a>
                @elseif ($businessProfile->verification_status === 'rejected')
                    <a href="{{ route('buyer.become-vendor') }}"
                        class="px-6 py-3 text-sm font-medium text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                        Submit New Application
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection

