@extends('layouts.home')

@section('page-content')
<!-- Header Section -->
<div class="mb-4 sm:mb-6 lg:mb-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-black text-gray-900 uppercase sm:text-2xl lg:text-lg">Business Profile Details</h1>
            <p class="mt-1 text-xs text-gray-600 sm:text-sm">Review business profile and documents</p>
        </div>
        <a href="{{ route('admin.business-profile.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-semibold">
            <i class="fas fa-arrow-left"></i>
            <span>Back to List</span>
        </a>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="p-4 mb-4 bg-green-50 rounded-lg border border-green-300">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
@endif

@if($errors->any())
    <div class="p-4 mb-4 bg-red-50 rounded-lg border border-red-300">
        <p class="mb-2 text-sm font-medium text-red-900">Please fix the following errors:</p>
        <ul class="space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Business Information Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
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
                        <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Owner Information Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Owner Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Owner Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->user->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $businessProfile->user->email ?? 'N/A' }}</p>
                    </div>
                    @if($ownerID)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID/Passport Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $ownerID->id_number ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Documents Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Submitted Documents</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Business Registration Document -->
                    @if($ownerID && $ownerID->business_document_path)
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Business Registration Document</label>
                            <a href="{{ Storage::url($ownerID->business_document_path) }}" target="_blank"
                                class="inline-flex gap-2 items-center px-4 py-2 text-sm font-medium text-white bg-[#ff0808] rounded-lg hover:bg-[#cc0606] transition-colors">
                                <i class="fas fa-file-pdf"></i>
                                View Document
                            </a>
                        </div>
                    @elseif($businessProfile->business_registration_doc)
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
                    @if($ownerID && $ownerID->id_document_path)
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
    </div>

    <!-- Sidebar Actions -->
    <div class="space-y-6">
        <!-- Status Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Status</h2>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                        <i class="mr-1.5 fas fa-clock"></i>
                        Pending Review
                    </span>
                </div>
                <div class="text-sm text-gray-600">
                    <p class="mb-1"><strong>Verification Status:</strong> {{ ucfirst($businessProfile->verification_status) }}</p>
                    <p><strong>Admin Verified:</strong> {{ $businessProfile->is_admin_verified ? 'Yes' : 'No' }}</p>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Actions</h2>
            </div>
            <div class="p-6 space-y-3">
                <!-- Verify Button -->
                <form action="{{ route('admin.business-profile.verify', $businessProfile) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to verify this business profile? This will create a vendor account.');">
                    @csrf
                    <button type="submit"
                        class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-check-circle"></i>
                        Verify & Create Vendor
                    </button>
                </form>

                <!-- Reject Button -->
                <form action="{{ route('admin.business-profile.reject', $businessProfile) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to reject this business profile?');">
                    @csrf
                    <div class="mb-3">
                        <label for="rejection_reason" class="block mb-1 text-sm font-medium text-gray-700">Rejection Reason (Optional)</label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="3"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Enter reason for rejection..."></textarea>
                    </div>
                    <button type="submit"
                        class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-times-circle"></i>
                        Reject Application
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

