@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('vendor.addons.index') }}" class="p-2 text-gray-600 hover:text-[#ff0808] rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Addon Details</h1>
                <p class="mt-1 text-xs text-gray-500">View and manage your addon subscription</p>
            </div>
        </div>

    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
            <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Status Banner -->
    @php
        $isActive = $addonUser->ended_at && $addonUser->ended_at->isFuture() && $addonUser->paid_at;
        $isExpired = $addonUser->ended_at && $addonUser->ended_at->isPast();
        $isPending = !$addonUser->paid_at;

        $statusConfig = [
            'active' => ['bg' => 'bg-green-500', 'text' => 'Active', 'icon' => 'fa-check-circle'],
            'expired' => ['bg' => 'bg-orange-500', 'text' => 'Expired', 'icon' => 'fa-clock'],
            'pending' => ['bg' => 'bg-yellow-500', 'text' => 'Pending Payment', 'icon' => 'fa-exclamation-circle'],
        ];

        $status = $isPending ? 'pending' : ($isActive ? 'active' : 'expired');
        $config = $statusConfig[$status];

        $locationColors = [
            'Homepage' => ['bg-pink-500', 'fa-home'],
            'Products' => ['bg-blue-500', 'fa-boxes'],
            'Suppliers' => ['bg-purple-500', 'fa-store'],
            'Marketplace' => ['bg-green-500', 'fa-shopping-bag'],
            'Category' => ['bg-orange-500', 'fa-list'],
            'Search' => ['bg-teal-500', 'fa-search'],
        ];
        $locationStyle = $locationColors[$addonUser->addon->locationX] ?? ['bg-gray-500', 'fa-map-marker-alt'];
    @endphp

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="{{ $locationStyle[0] }} p-5 text-white relative">
            <div class="absolute top-4 right-4">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white bg-opacity-20 rounded-full text-sm font-medium">
                    <i class="fas {{ $config['icon'] }}"></i>
                    {{ $config['text'] }}
                </span>
            </div>
            <div class="flex items-center gap-4 pr-32">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas {{ $locationStyle[1] }} text-3xl"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-1">{{ $addonUser->addon->locationX }}</h2>
                    <p class="text-white text-opacity-90 text-sm mb-2">{{ ucfirst(str_replace('_', ' ', $addonUser->addon->locationY)) }} Position</p>
                    <div class="flex flex-wrap items-center gap-3 text-xs">
                        <span class="flex items-center gap-1 bg-white bg-opacity-20 px-2 py-1 rounded">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $addonUser->addon->country ? $addonUser->addon->country->name : 'Global' }}
                        </span>
                        <span class="flex items-center gap-1 bg-white bg-opacity-20 px-2 py-1 rounded">
                            <i class="fas fa-calendar"></i>
                            {{ $addonUser->paid_days }} days subscription
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Promoted Item Details -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 bg-gray-50 border-b">
                    <h3 class="text-sm font-semibold text-gray-900">Promoted Item</h3>
                    <p class="text-xs text-gray-500 mt-1">Details about the item being featured</p>
                </div>
                <div class="p-5">
                    <div class="flex items-start gap-4">
                        @php
                            $typeConfig = [
                                'product' => ['name' => 'Product', 'icon' => 'fa-box', 'color' => 'blue'],
                                'supplier' => ['name' => 'Supplier Profile', 'icon' => 'fa-store', 'color' => 'purple'],
                                'showroom' => ['name' => 'Showroom', 'icon' => 'fa-building', 'color' => 'indigo'],
                                'tradeshow' => ['name' => 'Tradeshow', 'icon' => 'fa-calendar', 'color' => 'cyan'],
                                'loadboad' => ['name' => 'Load Board', 'icon' => 'fa-truck', 'color' => 'orange'],
                                'car' => ['name' => 'Car', 'icon' => 'fa-car', 'color' => 'green'],
                            ];
                            $type = $typeConfig[$addonUser->type] ?? ['name' => ucfirst($addonUser->type), 'icon' => 'fa-cube', 'color' => 'gray'];
                        @endphp

                        <div class="w-14 h-14 bg-{{ $type['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ $type['icon'] }} text-{{ $type['color'] }}-600 text-2xl"></i>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h4 class="text-base font-bold text-gray-900">{{ $type['name'] }}</h4>
                                <span class="px-2 py-0.5 bg-{{ $type['color'] }}-100 text-{{ $type['color'] }}-800 rounded-full text-xs font-medium">
                                    {{ ucfirst($addonUser->type) }}
                                </span>
                            </div>

                            @if($addonUser->type === 'product' && $addonUser->product)
                                <p class="text-sm text-gray-900 font-medium mb-1">{{ $addonUser->product->name }}</p>
                                <p class="text-xs text-gray-600">SKU: {{ $addonUser->product->sku ?? 'N/A' }}</p>
                            @elseif($addonUser->type === 'supplier' && $addonUser->supplier)
                                <p class="text-sm text-gray-900 font-medium mb-1">{{ $addonUser->supplier->business_name }}</p>
                                <p class="text-xs text-gray-600">{{ $addonUser->supplier->business_type }}</p>
                            @elseif($addonUser->type === 'showroom' && $addonUser->showroom)
                                <p class="text-sm text-gray-900 font-medium mb-1">{{ $addonUser->showroom->name }}</p>
                                <p class="text-xs text-gray-600">{{ $addonUser->showroom->address }}</p>
                            @elseif($addonUser->type === 'tradeshow' && $addonUser->tradeshow)
                                <p class="text-sm text-gray-900 font-medium mb-1">{{ $addonUser->tradeshow->name }}</p>
                                <p class="text-xs text-gray-600">{{ $addonUser->tradeshow->location }}</p>
                            @else
                                <p class="text-sm text-gray-600 italic">Item details not available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 bg-gray-50 border-b">
                    <h3 class="text-sm font-semibold text-gray-900">Subscription Timeline</h3>
                    <p class="text-xs text-gray-500 mt-1">Track your addon subscription progress</p>
                </div>
                <div class="p-5">
                    @if($addonUser->paid_at && $addonUser->ended_at)
                        @php
                            $totalDays = $addonUser->paid_at->diffInDays($addonUser->ended_at);
                            $daysElapsed = $addonUser->paid_at->diffInDays(now());
                            $daysRemaining = max(0, $addonUser->ended_at->diffInDays(now()));
                            $percentage = $totalDays > 0 ? min(100, ($daysElapsed / $totalDays) * 100) : 0;
                        @endphp

                        <div class="mb-4">
                            <div class="flex justify-between text-xs text-gray-600 mb-2">
                                <span>Started: {{ $addonUser->paid_at->format('M d, Y') }}</span>
                                <span>{{ number_format($percentage, 1) }}% Complete</span>
                                <span>Ends: {{ $addonUser->ended_at->format('M d, Y') }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-[#ff0808] h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3 mt-4">
                            <div class="text-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                <div class="text-2xl font-bold text-blue-600">{{ round($totalDays) }}</div>
                                <div class="text-xs text-gray-600 mt-1">Total Days</div>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-lg border border-green-100">
                                <div class="text-2xl font-bold text-green-600">{{ round($daysElapsed) }}</div>
                                <div class="text-xs text-gray-600 mt-1">Days Elapsed</div>
                            </div>
                            <div class="text-center p-3 bg-orange-50 rounded-lg border border-orange-100">
                                <div class="text-2xl font-bold text-orange-600">{{ round($daysRemaining) }}</div>
                                <div class="text-xs text-gray-600 mt-1">Days Remaining</div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-clock text-4xl text-gray-300 mb-3"></i>
                            <p class="text-sm text-gray-600">Payment pending - timeline will be available after payment</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Performance Insights -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 bg-gray-50 border-b">
                    <h3 class="text-sm font-semibold text-gray-900">Performance Insights</h3>
                    <p class="text-xs text-gray-500 mt-1">Track your addon effectiveness</p>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-eye text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-blue-600 font-medium">Impressions</div>
                                <div class="text-lg font-bold text-gray-900">Coming Soon</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-lg border border-purple-100">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-mouse-pointer text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-purple-600 font-medium">Clicks</div>
                                <div class="text-lg font-bold text-gray-900">Coming Soon</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-100">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-chart-line text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-green-600 font-medium">CTR</div>
                                <div class="text-lg font-bold text-gray-900">Coming Soon</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 bg-gray-50 border-b">
                    <h3 class="text-sm font-semibold text-gray-900">Quick Stats</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                        <span class="text-xs text-gray-600">Created</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $addonUser->created_at->format('M d, Y') }}</span>
                    </div>
                    @if($addonUser->paid_at)
                        <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                            <span class="text-xs text-gray-600">Activated</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $addonUser->paid_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                            <span class="text-xs text-gray-600">Expires</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $addonUser->ended_at->format('M d, Y') }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                        <span class="text-xs text-gray-600">Duration</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $addonUser->paid_days }} days</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600">Price</span>
                        <span class="text-lg font-bold text-[#ff0808]">${{ number_format($addonUser->addon->price, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 bg-gray-50 border-b">
                    <h3 class="text-sm font-semibold text-gray-900">Actions</h3>
                </div>
                <div class="p-5 space-y-2">
                    @if($isActive || $isExpired)
                        <a href="{{ route('vendor.addons.renew-form', $addonUser) }}" class="flex items-center gap-2 w-full px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm transition-all text-center justify-center">
                            <i class="fas fa-redo"></i>
                            <span>Repurchase Addon</span>
                        </a>
                    @endif


                    @if($isActive)
                        <form action="{{ route('vendor.addons.deactivate', $addonUser) }}" method="POST" onsubmit="return confirm('Are you sure you want to deactivate this addon?');">
                            @csrf
                            @method('POST')
                            <button type="submit" class="flex items-center gap-2 w-full px-4 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium text-sm transition-all text-center justify-center">
                                <i class="fas fa-pause-circle"></i>
                                <span>Deactivate</span>
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('vendor.addons.cancel', $addonUser) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this addon? This action cannot be undone.');">
                        @csrf
                        @method('POST')
                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-sm transition-all text-center justify-center">
                            <i class="fas fa-trash"></i>
                            <span>Cancel Addon</span>
                        </button>
                    </form>

                    <a href="{{ route('vendor.addons.index') }}" class="flex items-center gap-2 w-full px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-all text-center justify-center">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Addons</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Smooth transitions */
    * {
        transition-property: color, background-color, border-color, transform, box-shadow;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
</style>
@endsection
