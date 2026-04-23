@extends('layouts.home')

@section('page-content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.agents.index') }}" class="p-2 text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Create Agent</h1>
            <p class="text-xs text-gray-500">Create a new sales agent account</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
            <ul class="text-xs text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.agents.store') }}" method="POST"
          class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        @csrf

        <p class="text-sm font-semibold text-gray-700 border-b pb-2">Login Credentials</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                    placeholder="E.g. John Doe">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                    placeholder="agent@example.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                    placeholder="Min 8 characters">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                    placeholder="Re-enter password">
            </div>
        </div>

        <p class="text-sm font-semibold text-gray-700 border-b pb-2 pt-2">Agent Details</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}"
                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                    placeholder="Optional">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Commission Rate (%)</label>
                <input type="number" name="commission_rate" value="{{ old('commission_rate', 5) }}"
                    min="0" max="100" step="0.01" required
                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Code</label>
                <input type="text" name="phone_code" value="{{ old('phone_code', '+250') }}" required
                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                    placeholder="+250">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                    placeholder="788123456">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                <select name="country_id" required
                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-transparent bg-white">
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <input type="text" name="city" value="{{ old('city') }}" required
                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#ff0808] focus:border-transparent"
                    placeholder="Kigali">
            </div>
        </div>

        <div class="pt-2 flex justify-end gap-3">
            <a href="{{ route('admin.agents.index') }}"
               class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2.5 bg-[#ff0808] text-white rounded-lg text-sm font-semibold hover:bg-[#dd0606] transition-all shadow-sm">
                Create Agent
            </button>
        </div>
    </form>
</div>
@endsection
