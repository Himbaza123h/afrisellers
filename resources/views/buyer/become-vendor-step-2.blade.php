@extends('layouts.app')

@section('content')

    <div class="min-h-screen bg-white">
        <div class="px-6 py-12 mx-auto max-w-2xl">

            <!-- Progress Indicator -->
            <div class="mb-10">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center">
                        <div
                            class="flex justify-center items-center w-8 h-8 text-sm font-medium text-white bg-gray-900 rounded-full">
                            ✓
                        </div>&nbsp;
                        <span class="ml-3 text-sm font-medium text-gray-900">Business Information</span>
                    </div>
                    <div class="flex-1 mx-4 h-px bg-gray-900"></div>
                    <div class="flex items-center">
                        <div
                            class="flex justify-center items-center w-8 h-8 text-sm font-medium text-white bg-gray-900 rounded-full">
                            2
                        </div>&nbsp;
                        <span class="ml-3 text-sm font-medium text-gray-900">Documents</span>
                    </div>
                </div>
            </div>

            <!-- Header -->
            <div class="mb-10">
                <h1 class="mb-4 text-4xl font-bold text-gray-900">Upload Documents</h1>
                <p class="text-lg text-gray-600">Please upload the required documents to complete your vendor application.
                </p>
            </div>

            <!-- Business Summary -->
            <div class="p-4 mb-8 bg-gray-50 rounded-lg border border-gray-200">
                <p class="mb-2 text-xs text-gray-600">Business Information</p>
                <p class="text-sm font-medium text-gray-900">{{ $step1Data['business_name'] ?? '' }}</p>
                <p class="text-xs text-gray-600">Reg. Number: {{ $step1Data['business_registration_number'] ?? '' }}</p>
                @php
                    $country = \App\Models\Country::find($step1Data['country_id'] ?? null);
                @endphp
                <p class="text-xs text-gray-600">{{ $step1Data['city'] ?? '' }}{{ $country ? ', ' . $country->name : '' }}
                </p>
            </div>

            <!-- Error Messages -->
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

            <!-- Success Message -->
            @if (session('success'))
                <div class="p-4 mb-6 bg-green-50 rounded-lg border border-green-300">
                    <p class="text-sm text-green-900">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('buyer.become-vendor.step2.store', $businessProfile->id) }}" method="POST"
                class="space-y-6" enctype="multipart/form-data">
                @csrf

                <!-- Business Registration Document -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">
                        Business Registration Document
                    </label>
                    <div id="businessDocArea" onclick="document.getElementById('businessDoc').click()"
                        class="p-8 text-center rounded-lg border-2 border-gray-300 border-dashed transition-all cursor-pointer hover:border-gray-900 hover:bg-gray-50">
                        <input type="file" name="business_registration_doc" id="businessDoc" class="hidden"
                            accept=".pdf,.jpg,.jpeg,.png" required>
                        <svg class="mx-auto mb-3 w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <div class="mb-1 text-sm font-medium text-gray-900">Click to upload business certificate</div>
                        <div class="text-xs text-gray-500">PDF, JPG, PNG (Max 5MB)</div>
                    </div>
                    <div id="businessDocPreview" class="hidden p-3 mt-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 min-w-0">
                                <p id="businessDocName" class="text-sm font-medium text-gray-900 truncate"></p>
                                <p id="businessDocInfo" class="text-xs text-gray-600"></p>
                            </div>
                            <button type="button" onclick="removeFile('businessDoc')"
                                class="ml-3 text-sm text-gray-600 underline hover:text-gray-900">Remove</button>
                        </div>
                        <img id="businessDocImage" class="hidden mt-3 max-w-full h-auto rounded-lg border border-gray-200">
                    </div>
                </div>

                <!-- ID Number -->
                <div>
                    <label for="id_number" class="block mb-2 text-sm font-medium text-gray-900">
                        ID Number / Passport Number
                    </label>
                    <input type="text" name="id_number" id="id_number" value="{{ old('id_number') }}"
                        class="w-full px-4 py-3 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-4 focus:ring-gray-300 focus:border-gray-900 transition-colors"
                        placeholder="Enter your ID or Passport number" required>
                    @error('id_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Owner ID Document -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">
                        Owner ID Document (Passport/National ID)
                    </label>
                    <div id="ownerIdArea" onclick="document.getElementById('ownerId').click()"
                        class="p-8 text-center rounded-lg border-2 border-gray-300 border-dashed transition-all cursor-pointer hover:border-gray-900 hover:bg-gray-50">
                        <input type="file" name="owner_id_document" id="ownerId" class="hidden"
                            accept=".pdf,.jpg,.jpeg,.png" required>
                        <svg class="mx-auto mb-3 w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <div class="mb-1 text-sm font-medium text-gray-900">Click to upload ID or Passport</div>
                        <div class="text-xs text-gray-500">PDF, JPG, PNG (Max 5MB)</div>
                    </div>
                    <div id="ownerIdPreview" class="hidden p-3 mt-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 min-w-0">
                                <p id="ownerIdName" class="text-sm font-medium text-gray-900 truncate"></p>
                                <p id="ownerIdInfo" class="text-xs text-gray-600"></p>
                            </div>
                            <button type="button" onclick="removeFile('ownerId')"
                                class="ml-3 text-sm text-gray-600 underline hover:text-gray-900">Remove</button>
                        </div>
                        <img id="ownerIdImage" class="hidden mt-3 max-w-full h-auto rounded-lg border border-gray-200">
                    </div>
                </div>

                <!-- Security Info -->
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-700">
                        <span class="font-medium">Secure:</span> All documents are encrypted and stored securely. Your
                        information is protected.
                    </p>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex flex-col-reverse gap-3 pt-4 sm:flex-row">
                    <a href="{{ route('buyer.become-vendor') }}"
                        class="px-8 py-3 font-medium text-center text-gray-900 bg-white rounded-lg border border-gray-300 transition-colors hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-300">
                        Back
                    </a>
                    <button type="submit"
                        class="flex-1 px-8 py-3 bg-[#ff0808] text-white font-medium rounded-lg hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-colors sm:flex-initial">
                        Complete Application
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        input:focus,
        select:focus {
            --tw-ring-color: #111827;
        }
    </style>

    @push('scripts')
        <script>
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            }

            function setupFileUpload(inputId, areaId, previewId, nameId, infoId, imageId) {
                const input = document.getElementById(inputId);
                const area = document.getElementById(areaId);
                const preview = document.getElementById(previewId);
                const nameEl = document.getElementById(nameId);
                const infoEl = document.getElementById(infoId);
                const imageEl = document.getElementById(imageId);

                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    if (file.size > 5 * 1024 * 1024) {
                        alert('File size must be less than 5MB');
                        input.value = '';
                        return;
                    }

                    area.classList.remove('border-gray-300', 'hover:border-gray-900');
                    area.classList.add('border-gray-900', 'bg-gray-50');
                    preview.classList.remove('hidden');

                    nameEl.textContent = file.name;
                    infoEl.textContent = `${formatFileSize(file.size)} • ${file.type}`;

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imageEl.src = e.target.result;
                            imageEl.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        imageEl.classList.add('hidden');
                    }
                });
            }

            function removeFile(type) {
                const input = document.getElementById(type);
                const area = document.getElementById(type + 'Area');
                const preview = document.getElementById(type + 'Preview');
                const imageEl = document.getElementById(type + 'Image');

                input.value = '';
                area.classList.remove('border-gray-900', 'bg-gray-50');
                area.classList.add('border-gray-300', 'hover:border-gray-900');
                preview.classList.add('hidden');
                imageEl.classList.add('hidden');
                imageEl.src = '';
            }

            setupFileUpload('businessDoc', 'businessDocArea', 'businessDocPreview', 'businessDocName', 'businessDocInfo',
                'businessDocImage');
            setupFileUpload('ownerId', 'ownerIdArea', 'ownerIdPreview', 'ownerIdName', 'ownerIdInfo', 'ownerIdImage');

            document.querySelector('form').addEventListener('submit', function(e) {
                const idNumber = document.getElementById('id_number').value.trim();
                const businessDoc = document.getElementById('businessDoc').files[0];
                const ownerId = document.getElementById('ownerId').files[0];

                if (!idNumber) {
                    e.preventDefault();
                    alert('Please enter your ID or Passport number');
                    return false;
                }

                if (!businessDoc) {
                    e.preventDefault();
                    alert('Please upload business registration document');
                    return false;
                }

                if (!ownerId) {
                    e.preventDefault();
                    alert('Please upload owner ID document');
                    return false;
                }

                const submitBtn = e.target.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            });
        </script>
    @endpush
@endsection
