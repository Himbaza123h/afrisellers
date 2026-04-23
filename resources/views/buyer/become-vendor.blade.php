@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white">
    <div class="px-6 py-12 mx-auto max-w-2xl">

        <!-- Header -->
        <div class="mb-10">
            <h1 class="mb-4 text-4xl font-bold text-gray-900">Become a Vendor</h1>
            <p class="mb-8 text-lg text-gray-600">Start selling your products on AfriSellers and reach thousands of buyers across Africa.</p>
            <div class="flex flex-wrap gap-y-2 gap-x-8 text-sm text-gray-600">
                @foreach (['Quick Approval Process', '24/7 Support', 'Access to Thousands of Buyers'] as $benefit)
                    <div class="flex items-center">
                        <svg class="mr-2 w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ $benefit }}
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Account Summary -->
        <div class="p-4 mb-8 bg-gray-50 rounded-lg border border-gray-200">
            <p class="mb-1 text-xs text-gray-600">Registering as</p>
            <p class="font-medium text-gray-900">{{ auth()->user()->name }}</p>
            <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
        </div>

        @if (isset($existingProfile))
            @php
                $status = $existingProfile->verification_status;

                $badgeClasses = match($status) {
                    'verified' => 'bg-green-50 text-green-800 border border-green-200',
                    'rejected' => 'bg-red-50 text-red-800 border border-red-200',
                    default    => 'bg-amber-50 text-amber-800 border border-amber-200',
                };
                $dotClasses = match($status) {
                    'verified' => 'bg-green-500',
                    'rejected' => 'bg-red-500',
                    default    => 'bg-amber-400',
                };
                $badgeLabel = match($status) {
                    'verified' => 'Verified',
                    'rejected' => 'Rejected',
                    default    => 'Under Review',
                };
                $stepsDone = match($status) {
                    'verified' => 4,
                    'rejected' => 3,
                    default    => 2,
                };
            @endphp

            <!-- Status Badge -->
            <div class="flex items-center gap-3 mb-6">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium {{ $badgeClasses }}">
                    <span class="w-2 h-2 rounded-full {{ $dotClasses }}"></span>
                    {{ $badgeLabel }}
                </span>
                <span class="text-sm text-gray-500">Submitted {{ $existingProfile->created_at->format('M j, Y') }}</span>
            </div>

            <!-- Business Card -->
            <div class="p-5 mb-5 bg-gray-50 rounded-lg border border-gray-200">
                <p class="mb-1 text-xs text-gray-500 uppercase tracking-wide">Submitted Business</p>
                <p class="text-lg font-semibold text-gray-900">{{ $existingProfile->business_name }}</p>
                <p class="mb-4 text-sm text-gray-600">
                    {{ $existingProfile->city }}{{ $existingProfile->country ? ', ' . $existingProfile->country->name : '' }}
                </p>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Reg. Number</p>
                        <p class="text-gray-900 font-medium">{{ $existingProfile->business_registration_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Phone</p>
                        <p class="text-gray-900 font-medium">{{ $existingProfile->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Last Updated</p>
                        <p class="text-gray-900 font-medium">{{ $existingProfile->updated_at->format('M j, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="p-5 mb-5 bg-white rounded-lg border border-gray-200">
                <p class="mb-4 text-xs text-gray-500 uppercase tracking-wide">Application Progress</p>
                @php
                    $steps = [
                        ['label' => 'Business details submitted', 'desc' => 'Your business information has been received'],
                        ['label' => 'Documents uploaded',         'desc' => 'Business registration and ID documents received'],
                        ['label' => 'Admin review',               'desc' => $status === 'rejected' ? 'Your application was not approved' : 'Our team is reviewing your application'],
                        ['label' => 'Vendor account activated',   'desc' => 'You\'ll receive an email once approved'],
                    ];
                @endphp
                <div class="space-y-0">
                    @foreach ($steps as $i => $step)
                        @php
                            $num            = $i + 1;
                            $isDone         = $num < $stepsDone || ($status === 'verified' && $num === 4);
                            $isActive       = $num === $stepsDone && $status !== 'verified';
                            $isRejectedStep = $status === 'rejected' && $num === 3;

                            $iconBg = $isRejectedStep ? 'bg-red-100 text-red-700'
                                    : ($isDone        ? 'bg-green-100 text-green-700'
                                    : ($isActive      ? 'bg-amber-100 text-amber-700'
                                    :                   'bg-gray-100 text-gray-400'));

                            $textColor = ($isDone || $isActive) ? 'text-gray-900' : 'text-gray-400';
                            $descColor = ($isDone || $isActive) ? 'text-gray-500' : 'text-gray-300';
                        @endphp
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-medium flex-shrink-0 {{ $iconBg }}">
                                    @if ($isRejectedStep) ✕
                                    @elseif ($isDone) ✓
                                    @elseif ($isActive) ···
                                    @else {{ $num }}
                                    @endif
                                </div>
                                @if (!$loop->last)
                                    <div class="w-px flex-1 my-1 {{ ($isDone && !$isRejectedStep) ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                                @endif
                            </div>
                            <div class="pb-4">
                                <p class="text-sm font-medium {{ $textColor }}">{{ $step['label'] }}</p>
                                <p class="text-xs {{ $descColor }}">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ══ REJECTED — show reason + re-upload form ══ --}}
            @if ($status === 'rejected')

                <!-- Rejection Reason -->
                @if ($existingProfile->rejection_reason)
                    <div class="p-4 mb-5 bg-red-50 rounded-lg border border-red-200">
                        <p class="mb-1 text-sm font-medium text-red-900">Reason for Rejection</p>
                        <p class="text-sm text-red-700">{{ $existingProfile->rejection_reason }}</p>
                    </div>
                @endif

                <!-- Flash messages -->
                @if (session('success'))
                    <div class="p-4 mb-5 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-sm text-green-900">{{ session('success') }}</p>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="p-4 mb-5 bg-red-50 rounded-lg border border-red-200">
                        <p class="mb-1 text-sm font-medium text-red-900">Please fix the following:</p>
                        <ul class="space-y-1 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Re-upload Form -->
                <div class="p-5 mb-5 bg-white rounded-lg border border-gray-200">
                    <p class="mb-1 text-sm font-semibold text-gray-900">Submit Supporting Document</p>
                    <p class="mb-4 text-xs text-gray-500">
                        Upload an additional or corrected document to address the rejection reason.
                        Any previously submitted file will be replaced.
                    </p>

                    {{-- Show existing extra document if already uploaded --}}
                    @if ($existingProfile->extra_document)
                        <div class="flex items-center gap-3 p-3 mb-4 bg-gray-50 rounded-lg border border-gray-200">
                            <svg class="w-5 h-5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $existingProfile->extra_document_original_name ?? basename($existingProfile->extra_document) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Uploaded {{ $existingProfile->extra_document_uploaded_at?->format('M j, Y g:i A') ?? '—' }}
                                </p>
                            </div>
                            <a href="{{ Storage::url($existingProfile->extra_document) }}"
                               target="_blank"
                               class="text-xs text-blue-600 hover:underline flex-shrink-0">
                                View
                            </a>
                        </div>
                    @endif

                    <form action="{{ route('buyer.become-vendor.extra-document') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <label for="extra_document" class="block mb-2 text-sm font-medium text-gray-900">
                            {{ $existingProfile->extra_document ? 'Replace Document' : 'Upload Document' }}
                        </label>

                        <div id="drop-zone"
                             class="flex flex-col items-center justify-center gap-2 px-4 py-8 rounded-lg border-2 border-dashed border-gray-300 cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-colors"
                             onclick="document.getElementById('extra_document').click()">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                            </svg>
                            <p id="drop-label" class="text-sm text-gray-600">
                                <span class="font-medium text-gray-900">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-gray-400">PDF, JPG, JPEG or PNG — max 5 MB</p>
                        </div>

                        <input type="file" id="extra_document" name="extra_document"
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="hidden"
                               onchange="handleFileChange(this)">

                        <div class="flex flex-col-reverse gap-3 mt-4 sm:flex-row">
                            <a href="{{ route('buyer.dashboard.home') }}"
                               class="px-8 py-3 font-medium text-center text-gray-900 bg-white rounded-lg border border-gray-300 transition-colors hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-300">
                                Back to Dashboard
                            </a>
                            <button type="submit"
                                    class="flex-1 px-8 py-3 bg-[#ff0808] text-white font-medium rounded-lg hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors sm:flex-initial">
                                {{ $existingProfile->extra_document ? 'Replace & Resubmit' : 'Submit Document' }}
                            </button>
                        </div>
                    </form>
                </div>

            {{-- ══ VERIFIED ══ --}}
            @elseif ($status === 'verified')
                <div class="p-4 mb-5 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-sm font-medium text-green-900">Your vendor account is active!</p>
                    <p class="text-sm text-green-700">You can now access your vendor dashboard and start listing products.</p>
                </div>
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row">
                    <a href="{{ route('buyer.dashboard.home') }}"
                       class="px-8 py-3 font-medium text-center text-gray-900 bg-white rounded-lg border border-gray-300 transition-colors hover:bg-gray-50">
                        Back to Dashboard
                    </a>
                    <a href="{{ route('vendor.dashboard.home') }}"
                       class="flex-1 px-8 py-3 bg-[#ff0808] text-white font-medium rounded-lg hover:bg-red-800 text-center focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors sm:flex-initial">
                        Go to Vendor Dashboard
                    </a>
                </div>

            {{-- ══ PENDING ══ --}}
            @else
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row">
                    <a href="{{ route('buyer.dashboard.home') }}"
                       class="px-8 py-3 font-medium text-center text-gray-900 bg-white rounded-lg border border-gray-300 transition-colors hover:bg-gray-50">
                        Back to Dashboard
                    </a>
                    <span class="flex-1 px-8 py-3 bg-gray-100 text-gray-400 font-medium rounded-lg text-center cursor-not-allowed sm:flex-initial">
                        Application Pending…
                    </span>
                </div>
            @endif

        @else
            {{-- ══════════════════════════════════════════
                 NO EXISTING REQUEST — SHOW THE FORM
            ══════════════════════════════════════════ --}}

            @if ($errors->any())
                <div class="p-4 mb-6 bg-gray-50 rounded-lg border border-gray-300">
                    <p class="mb-2 text-sm font-medium text-red-900">{{ __('messages.fix_errors') }}</p>
                    <ul class="space-y-1 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="p-4 mb-6 bg-green-50 rounded-lg border border-green-300">
                    <p class="text-sm text-green-900">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('buyer.become-vendor.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="business_name" class="block mb-2 text-sm font-medium text-gray-900">Business Name</label>
                    <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}"
                        class="px-4 py-3 w-full text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        placeholder="Enter your business name" required>
                    <p class="mt-1.5 text-xs text-gray-500">The official name of your business</p>
                </div>

                <div>
                    <label for="business_registration_number" class="block mb-2 text-sm font-medium text-gray-900">Business Registration Number</label>
                    <input type="text" name="business_registration_number" id="business_registration_number"
                        value="{{ old('business_registration_number') }}"
                        class="px-4 py-3 w-full text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                        placeholder="Enter registration number" required>
                    <p class="mt-1.5 text-xs text-gray-500">Your official business registration or license number</p>
                </div>

                <div>
                    <label for="phone" class="block mb-2 text-sm font-medium text-gray-900">Business Phone Number</label>
                    <div class="flex gap-2">
                        <select name="phone_code"
                            class="px-3 py-3 text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            @foreach (['+250', '+254', '+255', '+256', '+234', '+233', '+27'] as $code)
                                <option value="{{ $code }}" {{ old('phone_code') == $code ? 'selected' : '' }}>{{ $code }}</option>
                            @endforeach
                        </select>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                            class="flex-1 px-4 py-3 text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                            placeholder="Business phone number" required>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Business Location</label>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <select name="country_id" id="country_id"
                            class="px-4 py-3 w-full text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                            required>
                            <option value="">Select Country</option>
                            @foreach ($countries ?? [] as $country)
                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" name="city" id="city" value="{{ old('city') }}"
                            class="px-4 py-3 w-full text-gray-900 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                            placeholder="City" required>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 pt-4 sm:flex-row">
                    <a href="{{ route('buyer.dashboard.home') }}"
                        class="px-8 py-3 font-medium text-center text-gray-900 bg-white rounded-lg border border-gray-300 transition-colors hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-300">
                        Cancel
                    </a>
                    <button type="submit"
                        class="flex-1 px-8 py-3 bg-[#ff0808] text-white font-medium rounded-lg hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors sm:flex-initial">
                        Next: Upload Documents
                    </button>
                </div>
            </form>
        @endif

        <div class="pt-8 mt-8 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Your application will be reviewed by our team. You'll receive an email notification once your vendor account is approved.
            </p>
        </div>
    </div>
</div>

<script>
    function handleFileChange(input) {
        const label = document.getElementById('drop-label');
        if (input.files && input.files[0]) {
            label.innerHTML = '<span class="font-medium text-gray-900">' + input.files[0].name + '</span>';
        }
    }

    const dropZone = document.getElementById('drop-zone');
    if (dropZone) {
        dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-gray-500', 'bg-gray-50'); });
        dropZone.addEventListener('dragleave', ()  => { dropZone.classList.remove('border-gray-500', 'bg-gray-50'); });
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('border-gray-500', 'bg-gray-50');
            const input = document.getElementById('extra_document');
            input.files = e.dataTransfer.files;
            handleFileChange(input);
        });
    }
</script>

<style>
    input:focus, select:focus { --tw-ring-color: #111827; }
</style>
@endsection
