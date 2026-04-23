@extends('layouts.home')

@section('page-content')
<div class="space-y-6">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <i class="fas fa-circle-check text-green-500"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <i class="fas fa-circle-xmark text-red-500"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.buyer.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Buyer Profile</h1>
            </div>
            <p class="text-xs text-gray-500 ml-6">Viewing full details for {{ $buyer->user->name ?? 'N/A' }}</p>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-2">
            {{-- Status actions --}}
            @if($buyer->account_status === 'pending')
                <form action="{{ route('admin.buyer.update-status', $buyer) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="account_status" value="active">
                    <button type="submit"
                        onclick="return confirm('Activate this buyer?')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        <i class="fas fa-check"></i> Activate
                    </button>
                </form>
            @endif

            @if($buyer->account_status === 'active')
                <form action="{{ route('admin.buyer.update-status', $buyer) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="account_status" value="suspended">
                    <button type="submit"
                        onclick="return confirm('Suspend this buyer?')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm font-medium">
                        <i class="fas fa-ban"></i> Suspend
                    </button>
                </form>
            @endif

            @if($buyer->account_status === 'suspended')
                <form action="{{ route('admin.buyer.update-status', $buyer) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="account_status" value="active">
                    <button type="submit"
                        onclick="return confirm('Reactivate this buyer?')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        <i class="fas fa-undo"></i> Reactivate
                    </button>
                </form>
            @endif

            <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Profile Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center">
                <!-- Avatar -->
                <div class="flex items-center justify-center w-20 h-20 rounded-full mx-auto mb-4 text-2xl font-bold text-white"
                     style="background: #ff0808;">
                    {{ strtoupper(substr($buyer->user->name ?? 'NA', 0, 2)) }}
                </div>

                <h2 class="text-lg font-bold text-gray-900">{{ $buyer->user->name ?? 'N/A' }}</h2>
                <p class="text-sm text-gray-500 mb-3">{{ $buyer->user->email ?? 'N/A' }}</p>

                <!-- Account Status Badge -->
                @php
                    $statusMap = [
                        'active'    => ['Active',    'bg-green-100 text-green-800'],
                        'pending'   => ['Pending',   'bg-yellow-100 text-yellow-800'],
                        'suspended' => ['Suspended', 'bg-red-100 text-red-800'],
                    ];
                    $statusInfo = $statusMap[$buyer->account_status] ?? ['Unknown', 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusInfo[1] }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5
                        {{ $buyer->account_status === 'active' ? 'bg-green-600' :
                           ($buyer->account_status === 'pending' ? 'bg-yellow-600' : 'bg-red-600') }}">
                    </span>
                    {{ $statusInfo[0] }}
                </span>

                <!-- Email Verification -->
                <div class="mt-3">
                    @if($buyer->email_verified)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            <i class="fas fa-check-circle mr-1.5 text-[10px]"></i> Email Verified
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                            <i class="fas fa-clock mr-1.5 text-[10px]"></i> Email Unverified
                        </span>
                    @endif
                </div>

                <!-- Member Since -->
                <p class="mt-4 text-xs text-gray-400">
                    Member since {{ $buyer->created_at->format('M d, Y') }}
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Stats</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500 flex items-center gap-2">
                            <i class="fas fa-calendar-plus w-4 text-center" style="color:#ff0808;"></i>
                            Joined
                        </span>
                        <span class="text-xs font-semibold text-gray-900">{{ $buyer->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500 flex items-center gap-2">
                            <i class="fas fa-clock w-4 text-center text-blue-500"></i>
                            Last Updated
                        </span>
                        <span class="text-xs font-semibold text-gray-900">{{ $buyer->updated_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500 flex items-center gap-2">
                            <i class="fas fa-venus-mars w-4 text-center text-purple-500"></i>
                            Gender
                        </span>
                        <span class="text-xs font-semibold text-gray-900">{{ ucfirst($buyer->sex ?? 'N/A') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-xs text-gray-500 flex items-center gap-2">
                            <i class="fas fa-id-badge w-4 text-center text-indigo-500"></i>
                            Buyer ID
                        </span>
                        <span class="text-xs font-mono font-semibold text-gray-900">#{{ $buyer->id }}</span>
                    </div>
                </div>
            </div>

            <!-- Switch Dashboard -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Admin Actions</h3>
                <button onclick="dashboardSwitch.open({{ $buyer->id }}, '{{ $buyer->user->name ?? 'Buyer' }}', 'Buyer', '{{ route('admin.buyer.switch-to-buyer', $buyer) }}')"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-blue-200 text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 text-sm font-medium transition-colors">
                    <i class="fas fa-sign-in-alt"></i>
                    Switch to Buyer Dashboard
                </button>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Personal Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-sm" style="color:#ff0808;"></i>
                    Personal Information
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Full Name</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $buyer->user->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Email Address</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $buyer->user->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Phone Number</label>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $buyer->phone_code ? '+' . $buyer->phone_code : '' }} {{ $buyer->phone ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Gender</label>
                        <p class="text-sm font-semibold text-gray-900">{{ ucfirst($buyer->sex ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Date of Birth</label>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $buyer->date_of_birth ? \Carbon\Carbon::parse($buyer->date_of_birth)->format('M d, Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">ID / Passport Number</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $buyer->id_passport_number ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-sm" style="color:#ff0808;"></i>
                    Location
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Country</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $buyer->country->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">City</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $buyer->city ?? 'N/A' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Address</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $buyer->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Business Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-briefcase text-sm" style="color:#ff0808;"></i>
                    Business Information
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Company Name</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $buyer->company_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Business Type</label>
                        <p class="text-sm font-semibold text-gray-900">{{ ucfirst($buyer->business_type ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Annual Purchase Volume</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $buyer->annual_purchase_volume ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Preferred Categories</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $buyer->preferred_categories ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-sliders-h text-sm" style="color:#ff0808;"></i>
                    Update Account Status
                </h3>

                <form action="{{ route('admin.buyer.update-status', $buyer) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 mb-2">Account Status</label>
                            <select name="account_status" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-300 text-sm">
                                <option value="active"    {{ $buyer->account_status === 'active'    ? 'selected' : '' }}>Active</option>
                                <option value="pending"   {{ $buyer->account_status === 'pending'   ? 'selected' : '' }}>Pending</option>
                                <option value="suspended" {{ $buyer->account_status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 text-white rounded-lg text-sm font-medium"
                            style="background:#ff0808;"
                            onmouseover="this.style.opacity='0.85'"
                            onmouseout="this.style.opacity='1'">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </div>
                </form>
            </div>

            <!-- Account Timestamps -->
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Account Timeline</h3>
                <div class="flex flex-col sm:flex-row gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Created At</label>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $buyer->created_at->format('M d, Y') }}
                            <span class="text-xs font-normal text-gray-500 ml-1">{{ $buyer->created_at->format('h:i A') }}</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $buyer->created_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $buyer->updated_at->format('M d, Y') }}
                            <span class="text-xs font-normal text-gray-500 ml-1">{{ $buyer->updated_at->format('h:i A') }}</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $buyer->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
