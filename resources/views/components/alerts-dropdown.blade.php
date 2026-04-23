@php
    use App\Models\SystemLog;
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $recentLogs = collect();
    $criticalCount = 0;

    if ($user) {
        $query = SystemLog::with('user')
            ->whereIn('level', ['warning', 'error', 'critical'])
            ->latest();

        if ($user->hasRole('admin')) {
            $recentLogs = $query->take(10)->get();
        } elseif ($user->country_admin && $user->country_id) {
            $recentLogs = $query->where(function ($q) use ($user) {
                $q->where('country_id', $user->country_id)
                  ->orWhereNull('country_id');
            })->take(10)->get();
        } else {
            $recentLogs = $query->whereNull('country_id')->take(10)->get();
        }

        $criticalCount = $recentLogs->whereIn('level', ['error', 'critical'])->count();
    }
@endphp

<div class="relative">
    <button id="alerts-btn"
        class="relative p-1.5 sm:p-2 hover:bg-gray-100 rounded-lg transition-colors"
        title="System Logs">
        <i class="fas fa-exclamation-triangle text-base sm:text-lg lg:text-xl text-orange-600"></i>
        @if($criticalCount > 0)
            <span id="alert-badge"
                class="absolute top-0 right-0 bg-orange-600 text-white text-[9px] sm:text-xs w-4 h-4 sm:w-5 sm:h-5 rounded-full flex items-center justify-center font-bold">
                {{ $criticalCount > 99 ? '99+' : $criticalCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div id="alerts-dropdown"
        class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-[32rem] overflow-hidden">

        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-orange-50">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-orange-500"></i>
                System Logs
            </h3>
            @if($user?->hasRole('admin'))
                <a href="{{ route('admin.system-logs.index') }}"
                   class="text-xs text-orange-600 hover:text-orange-700 font-semibold">
                    View All
                </a>
            @endif
        </div>

        {{-- Log List --}}
        <div class="overflow-y-auto max-h-96">
            @forelse($recentLogs as $log)
                @php
                    $colors = [
                        'critical' => ['border-l-red-500',    'bg-red-50',    'bg-red-500',    'text-red-900',    'bg-red-200 text-red-800'],
                        'error'    => ['border-l-orange-500', 'bg-orange-50', 'bg-orange-500', 'text-orange-900', 'bg-orange-200 text-orange-800'],
                        'warning'  => ['border-l-amber-500',  'bg-amber-50',  'bg-amber-500',  'text-amber-900',  'bg-amber-200 text-amber-800'],
                        'info'     => ['border-l-blue-500',   'bg-blue-50',   'bg-blue-500',   'text-blue-900',   'bg-blue-200 text-blue-800'],
                    ];
                    [$borderCls, $bgCls, $iconBgCls, $textCls, $badgeCls] = $colors[$log->level] ?? $colors['info'];
                @endphp
                <div class="flex items-start gap-3 px-4 py-3 {{ $bgCls }} border-b border-gray-100 border-l-4 {{ $borderCls }}">
                    <div class="flex-shrink-0 w-9 h-9 {{ $iconBgCls }} rounded-full flex items-center justify-center">
                        <i class="fas {{ $log->icon }} text-white text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="px-1.5 py-0.5 {{ $badgeCls }} text-[9px] font-bold rounded uppercase">
                                {{ $log->level }}
                            </span>
                            @if($log->module)
                                <span class="text-[9px] text-gray-500 font-semibold uppercase">
                                    {{ $log->module }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-800 font-medium leading-snug truncate">
                            {{ $log->description }}
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] text-gray-400">{{ $log->time_ago }}</span>
                            @if($log->user)
                                <span class="text-[10px] text-gray-400">· {{ $log->user->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-12 text-center">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-3"></i>
                    <p class="text-sm text-gray-500 font-medium">All Clear!</p>
                    <p class="text-xs text-gray-400 mt-1">No warnings or errors in the system</p>
                </div>
            @endforelse
        </div>

        @if($user?->hasRole('admin'))
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                <a href="{{ route('admin.system-logs.index') }}"
                   class="text-xs text-orange-600 hover:text-orange-700 font-semibold text-center block">
                    Open System Log Dashboard →
                </a>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn      = document.getElementById('alerts-btn');
    const dropdown = document.getElementById('alerts-dropdown');

    if (btn && dropdown) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
            const notifDropdown = document.getElementById('notifications-dropdown');
            if (notifDropdown) notifDropdown.classList.add('hidden');
        });

        document.addEventListener('click', function (e) {
            if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }
});

// Poll active count every 60s
setInterval(() => {
    fetch('/system-logs/active-count')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('alert-badge');
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                } else {
                    location.reload();
                }
            } else {
                if (badge) badge.remove();
            }
        })
        .catch(console.error);
}, 60000);
</script>
