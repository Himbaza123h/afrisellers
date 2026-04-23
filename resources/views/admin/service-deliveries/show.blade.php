@extends('layouts.home')

@section('page-content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.service-deliveries.index') }}"
           class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">{{ $serviceDelivery->service_name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Service delivery for
                <span class="font-medium text-gray-700">{{ $serviceDelivery->user->name }}</span>
            </p>
        </div>
        @php
            $statusColors = [
                'pending'     => 'bg-orange-100 text-orange-700 border-orange-200',
                'in_progress' => 'bg-blue-100 text-blue-700 border-blue-200',
                'delivered'   => 'bg-green-100 text-green-700 border-green-200',
                'rejected'    => 'bg-red-100 text-red-700 border-red-200',
            ];
            $statusIcons = [
                'pending'     => 'clock',
                'in_progress' => 'spinner',
                'delivered'   => 'check-circle',
                'rejected'    => 'times-circle',
            ];
        @endphp
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full border {{ $statusColors[$serviceDelivery->status] ?? '' }}">
            <i class="fas fa-{{ $statusIcons[$serviceDelivery->status] ?? 'circle' }} text-[10px]"></i>
            {{ ucfirst(str_replace('_', ' ', $serviceDelivery->status)) }}
        </span>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
        <i class="fas fa-check-circle text-green-600"></i>
        <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
        <button onclick="this.parentElement.remove()">
            <i class="fas fa-times text-green-500 hover:text-green-700"></i>
        </button>
    </div>
    @endif

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- ── LEFT: Service & Vendor Info ── -->
        <div class="space-y-5">

            <!-- Vendor Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Vendor</p>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 text-base font-bold text-purple-700">
                        {{ strtoupper(substr($serviceDelivery->user->name ?? 'V', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $serviceDelivery->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $serviceDelivery->user->email }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Plan</p>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold rounded-full">
                            <i class="fas fa-crown text-[9px]"></i>
                            {{ $serviceDelivery->plan->name }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Subscription</p>
                        <p class="text-xs font-medium text-gray-700">#{{ $serviceDelivery->subscription_id }}</p>
                    </div>
                </div>
            </div>

            <!-- Service Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Service Details</p>
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-xs text-gray-400">Service Name</p>
                        <p class="text-sm font-semibold text-gray-900 text-right">{{ $serviceDelivery->service_name }}</p>
                    </div>
                    <div class="flex items-start justify-between gap-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Feature Key</p>
                        <code class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $serviceDelivery->feature_key }}</code>
                    </div>
                    <div class="flex items-start justify-between gap-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Requested On</p>
                        <p class="text-sm font-medium text-gray-700">{{ $serviceDelivery->created_at->format('M d, Y') }}</p>
                    </div>
                    @if($serviceDelivery->delivered_at)
                    <div class="flex items-start justify-between gap-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Delivered On</p>
                        <p class="text-sm font-medium text-green-700">
                            <i class="fas fa-calendar-check mr-1 text-[10px]"></i>
                            {{ $serviceDelivery->delivered_at->format('M d, Y — H:i') }}
                        </p>
                    </div>
                    @endif
                    @if($serviceDelivery->deliveredBy)
                    <div class="flex items-start justify-between gap-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Delivered By</p>
                        <p class="text-sm font-medium text-gray-700">{{ $serviceDelivery->deliveredBy->name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Notes (if exists) -->
            @if($serviceDelivery->notes)
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Current Notes</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $serviceDelivery->notes }}</p>
            </div>
            @endif

        </div>

        <!-- ── RIGHT: Update Form ── -->
        <div class="space-y-5">

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-5">Update Status</p>

                <form action="{{ route('admin.service-deliveries.update-status', $serviceDelivery) }}"
                      method="POST"
                      class="space-y-4">
                    @csrf

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach([
                                ['value'=>'pending',     'label'=>'Pending',     'color'=>'orange', 'icon'=>'clock'],
                                ['value'=>'in_progress', 'label'=>'In Progress', 'color'=>'blue',   'icon'=>'spinner'],
                                ['value'=>'delivered',   'label'=>'Delivered',   'color'=>'green',  'icon'=>'check-circle'],
                                ['value'=>'rejected',    'label'=>'Rejected',    'color'=>'red',    'icon'=>'times-circle'],
                            ] as $opt)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status"
                                       value="{{ $opt['value'] }}"
                                       {{ $serviceDelivery->status === $opt['value'] ? 'checked' : '' }}
                                       class="peer sr-only">
                                <div class="flex items-center gap-2 px-3 py-2.5 rounded-lg border-2 border-gray-200
                                            peer-checked:border-{{ $opt['color'] }}-400
                                            peer-checked:bg-{{ $opt['color'] }}-50
                                            hover:border-gray-300 transition-all text-sm text-gray-600
                                            peer-checked:text-{{ $opt['color'] }}-700 peer-checked:font-semibold">
                                    <i class="fas fa-{{ $opt['icon'] }} text-xs opacity-60"></i>
                                    {{ $opt['label'] }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                            <span class="font-normal text-gray-400 text-xs ml-1">— visible to admin only</span>
                        </label>
                        <textarea name="notes" rows="4"
                                  placeholder="Describe what was done, links, or any relevant info..."
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent resize-none">{{ $serviceDelivery->notes }}</textarea>
                    </div>

                    <!-- Email Notify Toggle -->
                    <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <input type="checkbox" name="notify_user" value="1" id="notify_user"
                               {{ $serviceDelivery->status !== 'delivered' ? 'checked' : '' }}
                               class="mt-0.5 rounded accent-blue-600">
                        <label for="notify_user" class="text-sm text-blue-800 leading-snug cursor-pointer">
                            <span class="font-semibold">Email vendor</span> when status is set to
                            <span class="font-semibold">Delivered</span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ff0808] hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-all shadow-sm">
                            <i class="fas fa-save text-xs"></i>
                            Save Changes
                        </button>
                        <a href="{{ route('admin.service-deliveries.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-all">
                            <i class="fas fa-arrow-left text-xs"></i>
                            Back
                        </a>
                    </div>

                </form>
            </div>

            <!-- Delivery History hint -->
            <div class="bg-purple-50 to-indigo-50 rounded-xl border border-purple-100 p-5">
                <p class="text-xs font-bold text-purple-400 uppercase tracking-wider mb-3">Quick Info</p>
                <div class="space-y-2 text-sm text-gray-600">
                    <p class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-purple-400 mt-0.5 flex-shrink-0"></i>
                        Marking as <span class="font-semibold text-gray-800 mx-1">Delivered</span> with the email toggle on will automatically notify the vendor.
                    </p>
                    <p class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-purple-400 mt-0.5 flex-shrink-0"></i>
                        This service is part of the
                        <span class="font-semibold text-gray-800 mx-1">{{ $serviceDelivery->plan->name }}</span> plan.
                    </p>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
