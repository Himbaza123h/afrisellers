@extends('layouts.home')

@push('styles')
<style>
    @media print { .no-print { display: none !important; } }
</style>
@endpush

@section('page-content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Add New Vendor</h1>
            <p class="mt-1 text-xs text-gray-500">Create a vendor account with corresponding user</p>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <a href="{{ route('admin.business-profile.index') }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all font-medium shadow-sm text-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3 no-print">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3 no-print">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <div class="flex-1">
                @foreach($errors->all() as $error)
                    <p class="text-sm font-medium text-red-900">{{ $error }}</p>
                @endforeach
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.vendors.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- Left: Main Form --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Account Information --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-user text-[#ff0808]"></i> Account Information
                        </h2>
                    </div>
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none @error('name') border-red-400 @enderror"
                                placeholder="John Doe">
                            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none @error('email') border-red-400 @enderror"
                                placeholder="vendor@example.com">
                            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none @error('password') border-red-400 @enderror"
                                placeholder="Min 8 characters">
                            @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none"
                                placeholder="Repeat password">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text" name="phone_code" value="{{ old('phone_code', '+250') }}"
                                    class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none"
                                    placeholder="+250">
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none @error('phone') border-red-400 @enderror"
                                    placeholder="07XXXXXXXX">
                            </div>
                            @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                    </div>
                </div>

                {{-- Business Information --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-building text-[#ff0808]"></i> Business Information
                        </h2>
                    </div>
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Business Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="business_name" value="{{ old('business_name') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none @error('business_name') border-red-400 @enderror"
                                placeholder="Acme Ltd">
                            @error('business_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Registration Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="business_registration_number" value="{{ old('business_registration_number') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none @error('business_registration_number') border-red-400 @enderror"
                                placeholder="RB12345678">
                            @error('business_registration_number')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none @error('city') border-red-400 @enderror"
                                placeholder="Kigali">
                            @error('city')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <select name="country_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none @error('country_id') border-red-400 @enderror">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none resize-none"
                                placeholder="Brief description of the business...">{{ old('description') }}</textarea>
                        </div>

                    </div>
                </div>

                {{-- Documents --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-alt text-[#ff0808]"></i> Documents
                        </h2>
                    </div>
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Owner Full Name</label>
                            <input type="text" name="owner_full_name" value="{{ old('owner_full_name') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none"
                                placeholder="As it appears on ID">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Business Registration Doc</label>
                            <input type="file" name="business_registration_doc" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-red-50 file:text-[#ff0808] file:text-sm file:font-medium hover:file:bg-red-100">
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG — max 5MB</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Owner ID Document</label>
                            <input type="file" name="owner_id_document" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-red-50 file:text-[#ff0808] file:text-sm file:font-medium hover:file:bg-red-100">
                            <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG — max 5MB</p>
                        </div>

                    </div>
                </div>

            </div>{{-- end left --}}

            {{-- Right: Settings --}}
            <div class="space-y-4">

                {{-- Status & Plan --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-cog text-[#ff0808]"></i> Settings
                        </h2>
                    </div>
                    <div class="p-4 space-y-4">

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Account Status <span class="text-red-500">*</span>
                            </label>
                            <select name="account_status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none">
                                <option value="active"   @selected(old('account_status', 'active') === 'active')>Active</option>
                                <option value="verified" @selected(old('account_status') === 'verified')>Verified</option>
                                <option value="pending"  @selected(old('account_status') === 'pending')>Pending</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Membership Plan</label>
                            <select name="plan_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-200 focus:border-[#ff0808] outline-none">
                                <option value="">No Plan</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>
                                        {{ $plan->name }}
                                        @if($plan->price) ({{ number_format($plan->price) }} {{ $plan->currency }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                {{-- Submit --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 space-y-2">
                    <button type="submit"
                        class="w-full px-4 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Create Vendor
                    </button>
                    <a href="{{ route('admin.business-profile.index') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>

            </div>{{-- end right --}}

        </div>
    </form>

</div>
@endsection