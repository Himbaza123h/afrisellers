@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $isEdit ? 'Edit Referral' : 'Add New Referral' }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $isEdit ? 'Update referral information' : 'Add a new referral to your network' }}
            </p>
        </div>
        <a href="{{ route('agent.referrals.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm">
            <i class="fas fa-arrow-left"></i>
            <span>Back</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ $isEdit ? route('agent.referrals.update', $referral->id) : route('agent.referrals.store') }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="p-6 space-y-6">
                <!-- Referral Code (Display only when editing) -->
                @if($isEdit)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Referral Code
                        </label>
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <i class="fas fa-barcode text-gray-400 text-xl"></i>
                            <span class="text-lg font-mono font-bold text-gray-900">{{ $referral->referral_code }}</span>
                            <button type="button" onclick="copyCode('{{ $referral->referral_code }}')" class="ml-auto inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all text-sm font-medium">
                                <i class="fas fa-copy"></i>
                                <span>Copy</span>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">This unique code identifies this referral</p>
                    </div>
                @endif

                <!-- Personal Information Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i>
                        Personal Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $referral->name ?? '') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" id="email" name="email" value="{{ old('email', $referral->email ?? '') }}" required class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $referral->phone ?? '') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                            </div>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-flag text-blue-600"></i>
                        Referral Status
                    </h3>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                            <option value="">Select Status</option>
                            <option value="pending" {{ old('status', $referral->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ old('status', $referral->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $referral->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="rejected" {{ old('status', $referral->status ?? '') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                            <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-clock text-yellow-600"></i>
                                    <span class="text-xs font-semibold text-yellow-900">Pending</span>
                                </div>
                                <p class="text-xs text-yellow-700">Awaiting review or registration</p>
                            </div>
                            <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-check-circle text-green-600"></i>
                                    <span class="text-xs font-semibold text-green-900">Active</span>
                                </div>
                                <p class="text-xs text-green-700">Registered and active user</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-user-slash text-gray-600"></i>
                                    <span class="text-xs font-semibold text-gray-900">Inactive</span>
                                </div>
                                <p class="text-xs text-gray-700">No longer active</p>
                            </div>
                            <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-times-circle text-red-600"></i>
                                    <span class="text-xs font-semibold text-red-900">Rejected</span>
                                </div>
                                <p class="text-xs text-red-700">Referral was declined</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-blue-600"></i>
                        Additional Notes
                    </h3>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea id="notes" name="notes" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror" placeholder="Add any additional information about this referral...">{{ old('notes', $referral->notes ?? '') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Maximum 1000 characters</p>
                    </div>
                </div>
            </div>

            <!-- Form Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between gap-3">
                <a href="{{ route('agent.referrals.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium shadow-sm">
                    <i class="fas fa-{{ $isEdit ? 'save' : 'plus' }}"></i>
                    <span>{{ $isEdit ? 'Update Referral' : 'Add Referral' }}</span>
                </button>
            </div>
        </form>
    </div>

    @if($isEdit)
        <!-- Quick Actions Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-bolt text-yellow-500"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('agent.referrals.show', $referral->id) }}" class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg hover:bg-blue-100 transition-all">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-lg">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 text-sm">View Details</p>
                            <p class="text-xs text-gray-600">See full referral information</p>
                        </div>
                    </a>

                    <form action="{{ route('agent.referrals.destroy', $referral->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this referral? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center gap-3 p-4 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-lg hover:bg-red-100 transition-all">
                            <div class="flex items-center justify-center w-10 h-10 bg-red-600 text-white rounded-lg">
                                <i class="fas fa-trash"></i>
                            </div>
                            <div class="flex-1 text-left">
                                <p class="font-medium text-gray-900 text-sm">Delete Referral</p>
                                <p class="text-xs text-gray-600">Remove this referral permanently</p>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function copyCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        // Show success message
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i><span>Copied!</span>';
        btn.classList.add('bg-green-50', 'border-green-300', 'text-green-700');

        setTimeout(function() {
            btn.innerHTML = originalHTML;
            btn.classList.remove('bg-green-50', 'border-green-300', 'text-green-700');
        }, 2000);
    }, function(err) {
        alert('Failed to copy code: ' + err);
    });
}
</script>
@endsection
