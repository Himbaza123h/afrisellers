<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    /**
     * Display audit logs with filtering
     */
    public function index(Request $request)
    {
        // ── LOGS tab ──────────────────────────────────────────────
        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_type',  'like', "%{$search}%")
                  ->orWhere('ip_address',  'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) =>
                      $uq->where('name',  'like', "%{$search}%")
                         ->orWhere('email','like', "%{$search}%")
                  );
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model')) {
            $query->where('model_type', $request->model);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        // ── VISITORS tab ──────────────────────────────────────────
        $visitors = AuditLog::where('action', 'visited')
            ->with('user')
            ->when($request->filled('vsearch'), fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('ip_address', 'like', "%{$request->vsearch}%")
                       ->orWhere('country',   'like', "%{$request->vsearch}%")
                       ->orWhere('url',        'like', "%{$request->vsearch}%")
                )
            )
            ->when($request->filled('vcountry'), fn($q) => $q->where('country', $request->vcountry))
            ->orderByDesc('created_at')
            ->paginate(20, ['*'], 'vpage');

        $visitorCountries = AuditLog::where('action', 'visited')
            ->whereNotNull('country')->distinct()->pluck('country')->sort()->values();

        // ── BY-VENDOR tab ─────────────────────────────────────────
        $vendorActivityQuery = AuditLog::whereNotNull('user_id')
            ->select(
                'user_id',
                DB::raw('COUNT(*) as total_actions'),
                DB::raw("SUM(CASE WHEN action='created'  THEN 1 ELSE 0 END) as created_count"),
                DB::raw("SUM(CASE WHEN action='updated'  THEN 1 ELSE 0 END) as updated_count"),
                DB::raw("SUM(CASE WHEN action='deleted'  THEN 1 ELSE 0 END) as deleted_count"),
                DB::raw("SUM(CASE WHEN action='viewed'   THEN 1 ELSE 0 END) as viewed_count"),
                DB::raw("SUM(CASE WHEN action='visited'  THEN 1 ELSE 0 END) as visited_count"),
                DB::raw("SUM(CASE WHEN action='liked'    THEN 1 ELSE 0 END) as liked_count"),
                DB::raw("SUM(CASE WHEN action='shared'   THEN 1 ELSE 0 END) as shared_count"),
                DB::raw("SUM(CASE WHEN action='chat'     THEN 1 ELSE 0 END) as chat_count"),
                DB::raw("SUM(CASE WHEN action='comment'  THEN 1 ELSE 0 END) as comment_count"),
                DB::raw("SUM(CASE WHEN action='clicked'  THEN 1 ELSE 0 END) as clicked_count"),
                DB::raw("SUM(CASE WHEN action='exported' THEN 1 ELSE 0 END) as exported_count"),
                DB::raw("SUM(CASE WHEN action='login'    THEN 1 ELSE 0 END) as login_count"),
                DB::raw("SUM(CASE WHEN action='logout'   THEN 1 ELSE 0 END) as logout_count"),
                DB::raw('MAX(created_at) as last_activity')
            )
            ->groupBy('user_id');

        if ($request->filled('vendor_search')) {
            $vs = $request->vendor_search;
            $vendorActivityQuery->whereHas('user', fn($q) =>
                $q->where('name',  'like', "%{$vs}%")
                  ->orWhere('email','like', "%{$vs}%")
            );
        }

        $vendorActivities = $vendorActivityQuery
            ->orderByDesc('total_actions')
            ->paginate(15, ['*'], 'vdpage');

        // Eager-load users for vendor activities
        $userIds = $vendorActivities->pluck('user_id');
        $usersMap = User::whereIn('id', $userIds)->get()->keyBy('id');

        // ── SHARED helpers ────────────────────────────────────────
        $stats       = $this->getStats($request);
        $modelTypes  = AuditLog::select('model_type')->distinct()->whereNotNull('model_type')
                            ->pluck('model_type')
                            ->map(fn($t) => class_basename($t))
                            ->unique()->sort()->values();
        $actionTypes = ['created','updated','deleted','viewed','visited','liked','shared',
                         'chat','comment','clicked','exported','login','logout'];

        return view('admin.audit-logs.index', compact(
            'logs', 'stats', 'modelTypes', 'actionTypes',
            'visitors', 'visitorCountries',
            'vendorActivities', 'usersMap'
        ));
    }

    /**
     * Show specific audit log
     */
    public function show(AuditLog $log)
    {
        $log->load('user');
        $oldValues = $log->old_values ? json_decode($log->old_values, true) : null;
        $newValues = $log->new_values ? json_decode($log->new_values, true) : null;
        return view('admin.audit-logs.show', compact('log', 'oldValues', 'newValues'));
    }

    /**
     * Print audit logs report
     */
    public function print(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) =>
                $q->where('description','like',"%{$search}%")
                  ->orWhere('model_type','like',"%{$search}%")
                  ->orWhere('ip_address','like',"%{$search}%")
                  ->orWhereHas('user', fn($uq) =>
                      $uq->where('name','like',"%{$search}%")
                         ->orWhere('email','like',"%{$search}%")
                  )
            );
        }

        if ($request->filled('action'))    { $query->where('action',     $request->action); }
        if ($request->filled('model'))     { $query->where('model_type', $request->model);  }
        if ($request->filled('date_from')) { $query->whereDate('created_at', '>=', $request->date_from); }
        if ($request->filled('date_to'))   { $query->whereDate('created_at', '<=', $request->date_to);   }

        $logs  = $query->orderBy('created_at', 'desc')->get();
        $stats = $this->getStats($request);

        $actionDistribution = AuditLog::select('action', DB::raw('count(*) as count'))
            ->groupBy('action')->orderBy('count', 'desc')->get();

        return view('admin.audit-logs.print', compact('logs', 'stats', 'actionDistribution'));
    }

    /**
     * Print full vendor activity report (one vendor or all)
     */
    public function printVendor(Request $request, $userId = null)
    {
        if ($userId) {
            $users = User::whereIn('id', [$userId])->get();
        } else {
            $userIds = AuditLog::whereNotNull('user_id')->distinct()->pluck('user_id');
            $users   = User::whereIn('id', $userIds)->orderBy('name')->get();
        }

        $vendorData = [];
        foreach ($users as $user) {
            $logs = AuditLog::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->get();

            $actionCounts = $logs->groupBy('action')->map->count();

            $modelCounts = $logs->whereNotNull('model_type')
                ->groupBy(fn($l) => class_basename($l->model_type))
                ->map->count();

            $vendorData[] = [
                'user'         => $user,
                'logs'         => $logs,
                'actionCounts' => $actionCounts,
                'modelCounts'  => $modelCounts,
                'total'        => $logs->count(),
                'lastActivity' => $logs->first()?->created_at,
                'topCountry'   => $logs->whereNotNull('country')
                                       ->groupBy('country')->map->count()
                                       ->sortDesc()->keys()->first(),
            ];
        }

        return view('admin.audit-logs.print-vendor', compact('vendorData'));
    }

    /**
     * Export audit logs as CSV
     */
    public function export(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) =>
                $q->where('description','like',"%{$search}%")
                  ->orWhere('model_type','like',"%{$search}%")
            );
        }

        if ($request->filled('action'))    { $query->where('action',     $request->action); }
        if ($request->filled('model'))     { $query->where('model_type', $request->model);  }
        if ($request->filled('date_from')) { $query->whereDate('created_at', '>=', $request->date_from); }
        if ($request->filled('date_to'))   { $query->whereDate('created_at', '<=', $request->date_to);   }

        $logs     = $query->orderBy('created_at', 'desc')->get();
        $filename = 'audit-logs-' . now()->format('Y-m-d-His') . '.csv';

        return response()->stream(function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID','User','Action','Model','Description','IP','Country','City','Browser','Platform','URL','Date']);
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'System',
                    ucfirst($log->action),
                    $log->model_type ? class_basename($log->model_type) : 'N/A',
                    $log->description,
                    $log->ip_address,
                    $log->country  ?? '-',
                    $log->city     ?? '-',
                    $log->browser  ?? '-',
                    $log->platform ?? '-',
                    $log->url      ?? '-',
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Visitors method (standalone page — kept for backward compat)
     */
    public function visitors(Request $request)
    {
        $visitors = AuditLog::where('action', 'visited')
            ->with('user')
            ->when($request->filled('search'), fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('ip_address','like',"%{$request->search}%")
                       ->orWhere('country',  'like',"%{$request->search}%")
                       ->orWhere('url',       'like',"%{$request->search}%")
                )
            )
            ->when($request->filled('country'), fn($q) => $q->where('country', $request->country))
            ->orderByDesc('created_at')
            ->paginate(20);

        $countries = AuditLog::where('action','visited')
            ->whereNotNull('country')->distinct()->pluck('country')->sort()->values();

        return view('admin.audit-logs.visitors', compact('visitors', 'countries'));
    }

    // ── Private helpers ────────────────────────────────────────────────

    private function getStats($request): array
    {
        $q = AuditLog::query();
        if ($request->filled('date_from')) { $q->whereDate('created_at', '>=', $request->date_from); }
        if ($request->filled('date_to'))   { $q->whereDate('created_at', '<=', $request->date_to);   }

        return [
            'total_logs'      => $q->count(),
            'today_logs'      => (clone $q)->whereDate('created_at', today())->count(),
            'this_week_logs'  => (clone $q)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'actions_by_type' => AuditLog::select('action', DB::raw('count(*) as count'))
                ->groupBy('action')->pluck('count','action')->toArray(),
            'top_countries'   => AuditLog::select('country', DB::raw('count(*) as count'))
                ->whereNotNull('country')->groupBy('country')
                ->orderByDesc('count')->limit(5)->pluck('count','country')->toArray(),
            'top_cities'      => AuditLog::select('city', DB::raw('count(*) as count'))
                ->whereNotNull('city')->groupBy('city')
                ->orderByDesc('count')->limit(5)->pluck('count','city')->toArray(),
            'top_browsers'    => AuditLog::select('browser', DB::raw('count(*) as count'))
                ->whereNotNull('browser')->groupBy('browser')
                ->orderByDesc('count')->limit(5)->pluck('count','browser')->toArray(),
            'unique_ips'      => (clone $q)->whereNotNull('ip_address')->distinct('ip_address')->count('ip_address'),
            'visitor_logs'    => (clone $q)->where('action','visited')->count(),
            'active_users'    => AuditLog::whereNotNull('user_id')->whereDate('created_at', today())
                ->distinct('user_id')->count('user_id'),
        ];
    }
}
