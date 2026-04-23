@extends('layouts.home')

@section('page-content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Service Deliveries</h1>
            <p class="text-sm text-gray-500 mt-1">Track and manage manually delivered services per vendor</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label'=>'Total',       'value'=>$stats['total'],       'color'=>'gray',   'icon'=>'list'],
            ['label'=>'Pending',     'value'=>$stats['pending'],     'color'=>'orange', 'icon'=>'clock'],
            ['label'=>'In Progress', 'value'=>$stats['in_progress'], 'color'=>'blue',   'icon'=>'spinner'],
            ['label'=>'Delivered',   'value'=>$stats['delivered'],   'color'=>'green',  'icon'=>'check-circle'],
        ] as $s)
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-{{ $s['color'] }}-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-{{ $s['icon'] }} text-{{ $s['color'] }}-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $s['value'] }}</p>
                <p class="text-xs text-gray-500">{{ $s['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Vendor name, email, service..."
                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg w-56 focus:ring-2 focus:ring-red-400">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400">
                <option value="">All Statuses</option>
                <option value="pending"     {{ request('status')==='pending'     ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status')==='in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="delivered"   {{ request('status')==='delivered'   ? 'selected' : '' }}>Delivered</option>
                <option value="rejected"    {{ request('status')==='rejected'    ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-[#ff0808] text-white text-sm rounded-lg hover:bg-red-700 font-medium">Filter</button>
        <a href="{{ route('admin.service-deliveries.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 font-medium">Reset</a>
    </form>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
        <i class="fas fa-check-circle text-green-600"></i>
        <p class="text-sm font-medium text-green-900 flex-1">{{ session('success') }}</p>
        <button onclick="this.parentElement.remove()"><i class="fas fa-times text-green-600"></i></button>
    </div>
    @endif

    <!-- Vendor Groups -->
    <div class="space-y-3">
    @forelse($deliveries as $userId => $userDeliveries)
    @php
        $vendor       = $userDeliveries->first()->user;
        $pendingCount = $userDeliveries->where('status', 'pending')->count();
        $doneCount    = $userDeliveries->where('status', 'delivered')->count();
        $inProgCount  = $userDeliveries->where('status', 'in_progress')->count();
        $total        = $userDeliveries->count();
        $allDone      = $pendingCount === 0 && $inProgCount === 0;
        $planName     = $userDeliveries->first()->plan->name ?? '—';
        $autoOpen     = $pendingCount > 0 || $inProgCount > 0;
    @endphp

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        <!-- ── Vendor Header ── -->
        <button type="button"
                onclick="toggleVendor({{ $userId }})"
                class="w-full flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors text-left">

            <!-- Avatar -->
            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 text-sm font-bold text-purple-700">
                {{ strtoupper(substr($vendor->name ?? 'V', 0, 1)) }}
            </div>

            <!-- Name + meta -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-sm font-bold text-gray-900 leading-tight">{{ $vendor->name ?? 'Unknown' }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-semibold rounded-full border border-amber-200">
                        <i class="fas fa-crown mr-1 text-[9px]"></i>{{ $planName }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">{{ $vendor->email ?? '' }}</p>
            </div>

            <!-- Counts + progress -->
            <div class="hidden sm:flex items-center gap-3">
                @if($pendingCount > 0)
                <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-bold rounded-full">{{ $pendingCount }} pending</span>
                @endif
                @if($inProgCount > 0)
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full">{{ $inProgCount }} in progress</span>
                @endif
                @if($allDone)
                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">All done ✓</span>
                @endif

                <!-- Mini progress -->
                <div class="w-20">
                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full {{ $allDone ? 'bg-green-500' : 'bg-blue-500' }}"
                             style="width: {{ $total > 0 ? round($doneCount/$total*100) : 0 }}%"></div>
                    </div>
                    <p class="text-[9px] text-gray-400 mt-0.5 text-right">{{ $doneCount }}/{{ $total }}</p>
                </div>
            </div>

<i id="chevron-{{ $userId }}"
               class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200 flex-shrink-0 ml-1"></i>
        </button>

        <!-- Bulk Update Form -->
        <form action="{{ route('admin.service-deliveries.bulk-update', $userId) }}"
              method="POST"
              class="flex items-center gap-2 px-5 py-2.5 border-t border-gray-100 bg-gray-50 flex-wrap"
              onsubmit="return confirm('Update ALL services for this vendor?')">
            @csrf
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-1">Bulk Update:</span>
            <select name="status"
                    class="px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 bg-white">
                <option value="pending"     {{ $allDone ? '' : '' }}>Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="delivered">Delivered</option>
                <option value="rejected">Rejected</option>
            </select>
            <input type="text" name="notes" placeholder="Notes (optional)"
                   class="px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-400 flex-1 min-w-32 bg-white">
            <label class="flex items-center gap-1.5 text-xs text-blue-700 cursor-pointer">
                <input type="checkbox" name="notify_user" value="1" checked class="rounded accent-blue-600">
                Email vendor
            </label>
            <button type="submit"
                    class="px-3 py-1.5 bg-[#ff0808] hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5">
                <i class="fas fa-save text-[10px]"></i>
                Apply to All
            </button>
        </form>

        <!-- ── Services List ── -->
        <div id="vendor-{{ $userId }}" class="{{ $autoOpen ? '' : 'hidden' }}">
            @foreach($userDeliveries as $delivery)
            @php
                $statusColors = [
                    'pending'     => 'bg-orange-100 text-orange-700',
                    'in_progress' => 'bg-blue-100 text-blue-700',
                    'delivered'   => 'bg-green-100 text-green-700',
                    'rejected'    => 'bg-red-100 text-red-700',
                ];
                $statusIcons = [
                    'pending'     => 'clock',
                    'in_progress' => 'spinner',
                    'delivered'   => 'check-circle',
                    'rejected'    => 'times-circle',
                ];
            @endphp

            <div class="flex items-center gap-0 border-t border-gray-100 hover:bg-gray-50 transition-colors group">

                <!-- Left indent line -->
                <div class="flex items-center flex-shrink-0 self-stretch">
                    <!-- vertical line -->
                    <div class="w-px bg-gray-200 self-stretch ml-9"></div>
                    <!-- horizontal connector -->
                    <div class="w-5 h-px bg-gray-200"></div>
                    <!-- dot -->
                    <div class="w-1.5 h-1.5 rounded-full flex-shrink-0
                        {{ $delivery->status === 'delivered' ? 'bg-green-400' :
                           ($delivery->status === 'in_progress' ? 'bg-blue-400' :
                           ($delivery->status === 'rejected' ? 'bg-red-400' : 'bg-orange-400')) }}">
                    </div>
                </div>

                <!-- Service Info -->
                <div class="flex-1 flex items-center gap-4 px-4 py-3 min-w-0">

                    <!-- Icon + Name -->
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-tools text-indigo-400 text-[10px]"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $delivery->service_name }}</p>
                            <code class="text-[10px] text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded hidden sm:inline">{{ $delivery->feature_key }}</code>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <span class="flex-shrink-0 inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold rounded-full {{ $statusColors[$delivery->status] ?? '' }}">
                        <i class="fas fa-{{ $statusIcons[$delivery->status] ?? 'circle' }} text-[9px]"></i>
                        {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                    </span>

                    <!-- Notes preview -->
                    @if($delivery->notes)
                    <p class="hidden lg:block text-xs text-gray-400 truncate max-w-xs">{{ $delivery->notes }}</p>
                    @endif

                    <!-- Delivered date -->
                    @if($delivery->delivered_at)
                    <span class="hidden lg:block text-[10px] text-gray-400 flex-shrink-0">
                        <i class="fas fa-calendar-check mr-1"></i>{{ $delivery->delivered_at->format('M d, Y') }}
                    </span>
                    @endif
                </div>

                <!-- Action Icons -->
                <div class="flex items-center gap-1 px-4 flex-shrink-0">
                    @if($delivery->status !== 'delivered')
                    <!-- Manage (edit) -->
                    <a href="{{ route('admin.service-deliveries.show', $delivery) }}"
                       title="Manage"
                       class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-[#ff0808] hover:text-white transition-all">
                        <i class="fas fa-sliders-h text-xs"></i>
                    </a>
                    @else
                    <!-- View only -->
                    <a href="{{ route('admin.service-deliveries.show', $delivery) }}"
                       title="View"
                       class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-green-500 hover:text-white transition-all">
                        <i class="fas fa-eye text-xs"></i>
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-200 px-6 py-20 text-center">
        <i class="fas fa-tasks text-4xl text-gray-200 mb-4 block"></i>
        <p class="text-base font-semibold text-gray-900 mb-1">No service deliveries found</p>
        <p class="text-sm text-gray-500 mt-1">
            Run <code class="bg-gray-100 px-2 py-0.5 rounded text-xs font-mono">php artisan services:backfill</code> to populate existing subscriptions
        </p>
    </div>
    @endforelse
    </div>

</div>

<script>
function toggleVendor(userId) {
    const panel = document.getElementById('vendor-' + userId);
    const chev  = document.getElementById('chevron-' + userId);
    const isOpen = !panel.classList.contains('hidden');
    panel.classList.toggle('hidden', isOpen);
    chev.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

// Rotate chevron for auto-opened vendors on load
document.addEventListener('DOMContentLoaded', function () {
    @foreach($deliveries as $userId => $userDeliveries)
        @if($userDeliveries->where('status','pending')->count() > 0 || $userDeliveries->where('status','in_progress')->count() > 0)
        document.getElementById('chevron-{{ $userId }}').style.transform = 'rotate(180deg)';
        @endif
    @endforeach
});
</script>
@endsection
