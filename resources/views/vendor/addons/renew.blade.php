@extends('layouts.home')

@section('page-content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('vendor.addons.index') }}" class="p-2 text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Renew Addon</h1>
            <p class="mt-1 text-sm text-gray-500">Extend your promotional addon subscription</p>
        </div>
    </div>

    <!-- Messages -->
    @if(session('error'))
        <div class="p-4 bg-red-50 rounded-lg border border-red-200 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
            <p class="text-sm font-medium text-red-900 flex-1">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Current Addon Details -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-purple-500 to-indigo-600 p-6 text-white">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 rounded-full text-sm mb-3">
                        @if($addonUser->isActive())
                            <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                            <span>Active</span>
                        @elseif($addonUser->isExpired())
                            <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                            <span>Expired</span>
                        @endif
                    </div>
                    <h2 class="text-2xl font-bold mb-2">{{ $addonUser->addon->locationX }}</h2>
                    <p class="text-purple-100">{{ ucfirst(str_replace('_', ' ', $addonUser->addon->locationY)) }}</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">${{ number_format($addonUser->addon->price, 2) }}</div>
                    <div class="text-sm text-purple-100">per 30 days</div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">Type</div>
                    <div class="text-sm font-semibold text-gray-900 capitalize">{{ $addonUser->type }}</div>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">Item</div>
                    <div class="text-sm font-semibold text-gray-900">{{ $addonUser->related_entity->name ?? 'N/A' }}</div>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-xs text-gray-500 mb-1">Location</div>
                    <div class="text-sm font-semibold text-gray-900">
                        {{ $addonUser->addon->country ? $addonUser->addon->country->name : 'Global' }}
                    </div>
                </div>
            </div>

            @if($addonUser->ended_at)
                <div class="mt-4 p-4 {{ $addonUser->isExpired() ? 'bg-red-50 border-red-200' : 'bg-blue-50 border-blue-200' }} border rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas {{ $addonUser->isExpired() ? 'fa-exclamation-triangle text-red-600' : 'fa-info-circle text-blue-600' }} text-xl"></i>
                        <div class="flex-1">
                            <div class="font-semibold {{ $addonUser->isExpired() ? 'text-red-900' : 'text-blue-900' }} mb-1">
                                @if($addonUser->isExpired())
                                    Addon Expired
                                @else
                                    Expiring Soon
                                @endif
                            </div>
                            <div class="text-sm {{ $addonUser->isExpired() ? 'text-red-700' : 'text-blue-700' }}">
                                @if($addonUser->isExpired())
                                    This addon expired on {{ $addonUser->ended_at->format('M d, Y') }}
                                @else
                                    @php
                                        $daysLeft = round($addonUser->days_remaining);
                                    @endphp
                                    This addon will expire on {{ $addonUser->ended_at->format('M d, Y') }}
                                    ({{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }} remaining)
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Renewal Form -->
    <form action="{{ route('vendor.addons.renew', $addonUser) }}" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
        @csrf

        <!-- Duration Selection -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-3">
                Select Renewal Duration
                <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    30 => ['1 Month', '0%', 'Most Popular'],
                    60 => ['2 Months', '5%', 'Good Value'],
                    90 => ['3 Months', '10%', 'Better Deal'],
                    180 => ['6 Months', '15%', 'Best Value']
                ] as $days => $data)
                    <label class="relative">
                        <input type="radio" name="duration_days" value="{{ $days }}" class="peer sr-only" required {{ $days === 30 ? 'checked' : '' }} onchange="calculateRenewalPrice()">
                        <div class="h-full p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 hover:shadow-md">
                            @if($data[1] !== '0%')
                                <div class="absolute -top-2 -right-2 px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-lg">
                                    {{ $data[1] }} OFF
                                </div>
                            @endif
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-900 mb-1">{{ $data[0] }}</div>
                                <div class="text-sm text-gray-600 mb-2">{{ $days }} days</div>
                                <div class="text-xs font-medium text-purple-600">{{ $data[2] }}</div>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <div class="text-xs text-gray-500">Price</div>
                                    <div class="text-lg font-bold text-gray-900" data-days="{{ $days }}">
                                        $<span class="price-{{ $days }}">{{ number_format($addonUser->addon->price * ($days / 30), 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('duration_days')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Benefits -->
        <div class="p-6 bg-purple-50 to-indigo-50 rounded-xl border border-purple-200">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-star text-yellow-500 mr-2"></i>
                Renewal Benefits
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <div>
                        <div class="font-medium text-gray-900">No Interruption</div>
                        <div class="text-sm text-gray-600">Seamless continuation of your promotion</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <div>
                        <div class="font-medium text-gray-900">Extended Visibility</div>
                        <div class="text-sm text-gray-600">Keep your item in premium position</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <div>
                        <div class="font-medium text-gray-900">Bulk Discounts</div>
                        <div class="text-sm text-gray-600">Save more with longer durations</div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <div>
                        <div class="font-medium text-gray-900">Instant Activation</div>
                        <div class="text-sm text-gray-600">Renewed immediately after payment</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Price Summary -->
        <div class="p-6 bg-gray-50 to-gray-100 rounded-xl border border-gray-200">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Renewal Summary</h3>

            <div class="space-y-3 mb-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-600">Base Price (30 days)</span>
                    <span class="text-sm font-bold text-gray-900">${{ number_format($addonUser->addon->price, 2) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-600">Duration</span>
                    <span class="text-sm font-bold text-gray-900" id="renewalDurationDisplay">30 days</span>
                </div>
                <div class="flex items-center justify-between text-green-600" id="discountRow" style="display: none;">
                    <span class="text-sm font-medium">Discount</span>
                    <span class="text-sm font-bold" id="discountAmount">-$0.00</span>
                </div>
            </div>

            <div class="pt-4 border-t-2 border-gray-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-lg font-bold text-gray-900">Total Amount</span>
                    <span class="text-2xl font-bold text-purple-600" id="renewalTotalPrice">${{ number_format($addonUser->addon->price, 2) }}</span>
                </div>
                <div class="text-sm text-gray-600" id="newExpiryDate">
                    @if($addonUser->isActive())
                        New expiry: {{ $addonUser->ended_at->addDays(30)->format('M d, Y') }}
                    @else
                        New expiry: {{ now()->addDays(30)->format('M d, Y') }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-3">
            <a href="{{ route('vendor.addons.index') }}" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-center transition-all">
                Cancel
            </a>
            <button type="submit" class="flex-1 px-6 py-3 bg-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 font-medium shadow-lg hover:shadow-xl transition-all">
                <i class="fas fa-redo mr-2"></i>
                Renew Addon
            </button>
        </div>
    </form>
</div>

<script>
const basePrice = {{ $addonUser->addon->price }};
const isActive = {{ $addonUser->isActive() ? 'true' : 'false' }};
const currentEndDate = new Date('{{ $addonUser->ended_at ? $addonUser->ended_at->format('Y-m-d') : now()->format('Y-m-d') }}');

function calculateRenewalPrice() {
    const durationRadio = document.querySelector('input[name="duration_days"]:checked');
    if (!durationRadio) return;

    const days = parseInt(durationRadio.value);
    const months = days / 30;

    // Calculate discount
    let discount = 0;
    if (days === 60) discount = 0.05;
    else if (days === 90) discount = 0.10;
    else if (days === 180) discount = 0.15;

    const totalBeforeDiscount = basePrice * months;
    const discountAmount = totalBeforeDiscount * discount;
    const total = totalBeforeDiscount - discountAmount;

    // Update displays
    document.getElementById('renewalDurationDisplay').textContent = days + ' days';
    document.getElementById('renewalTotalPrice').textContent = '$' + total.toFixed(2);

    // Show/hide discount row
    const discountRow = document.getElementById('discountRow');
    if (discount > 0) {
        discountRow.style.display = 'flex';
        document.getElementById('discountAmount').textContent = '-$' + discountAmount.toFixed(2);
    } else {
        discountRow.style.display = 'none';
    }

    // Calculate new expiry date
    const baseDate = isActive ? currentEndDate : new Date();
    const newExpiryDate = new Date(baseDate);
    newExpiryDate.setDate(newExpiryDate.getDate() + days);

    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    document.getElementById('newExpiryDate').textContent =
        'New expiry: ' + newExpiryDate.toLocaleDateString('en-US', options);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateRenewalPrice();
});
</script>
@endsection
