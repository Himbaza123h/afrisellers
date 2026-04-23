<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemLogController extends Controller
{
    // ─── DROPDOWN DATA (AJAX) ─────────────────────────────────────────
    public function recent()
    {
        $user  = Auth::user();
        $query = SystemLog::with('user', 'country')
            ->whereIn('level', ['warning', 'error', 'critical'])
            ->latest();

        if (!$user->hasRole('admin')) {
            if ($user->country_admin && $user->country_id) {
                $query->where(function ($q) use ($user) {
                    $q->where('country_id', $user->country_id)
                      ->orWhereNull('country_id');
                });
            } else {
                $query->whereNull('country_id');
            }
        }

        $logs        = $query->take(10)->get();
        $criticalCount = $logs->whereIn('level', ['error', 'critical'])->count();

        return response()->json([
            'logs'           => $logs,
            'critical_count' => $criticalCount,
        ]);
    }

    // ─── ACTIVE COUNT (POLLING) ───────────────────────────────────────
    public function activeCount()
    {
        $user  = Auth::user();
        $query = SystemLog::whereIn('level', ['error', 'critical'])
            ->where('created_at', '>=', now()->subHours(24));

        if (!$user->hasRole('admin')) {
            if ($user->country_admin && $user->country_id) {
                $query->where(function ($q) use ($user) {
                    $q->where('country_id', $user->country_id)
                      ->orWhereNull('country_id');
                });
            } else {
                $query->whereNull('country_id');
            }
        }

        return response()->json(['count' => $query->count()]);
    }

    // ─── ADMIN INDEX ──────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = SystemLog::with('user', 'country')->latest();

        // Filters
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->paginate(50)->withQueryString();

        // Stats
        $stats = [
            'total'    => SystemLog::count(),
            'critical' => SystemLog::whereIn('level', ['critical', 'error'])->recent(24)->count(),
            'warning'  => SystemLog::level('warning')->recent(24)->count(),
            'today'    => SystemLog::whereDate('created_at', today())->count(),
        ];


        $modules = SystemLog::distinct()->pluck('module')->filter()->sort()->values();
        $actions = SystemLog::distinct()->pluck('action')->filter()->sort()->values();

        return view('admin.system-logs.index', compact('logs', 'stats', 'modules', 'actions'));
    }

    // ─── SHOW ─────────────────────────────────────────────────────────
    public function show($id)
    {
        $log = SystemLog::with('user', 'country')->findOrFail($id);
        return view('admin.system-logs.show', compact('log'));
    }

    // ─── PURGE OLD LOGS ───────────────────────────────────────────────
    public function purge(Request $request)
    {
        $days = $request->validate(['days' => 'required|integer|min:7|max:365'])['days'];

        $deleted = SystemLog::where('created_at', '<', now()->subDays($days))
            ->whereNotIn('level', ['critical', 'error'])
            ->delete();

        return back()->with('success', "{$deleted} log entries purged (older than {$days} days, info/warning only).");
    }
}
