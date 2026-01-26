<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
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
        $query = AuditLog::with('user');

        // Search by description or user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model')) {
            $query->where('model_type', $request->model);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get stats
        $stats = $this->getStats($request);

        // Get unique model types for filter
        $modelTypes = AuditLog::select('model_type')
            ->distinct()
            ->whereNotNull('model_type')
            ->pluck('model_type')
            ->map(function($type) {
                return class_basename($type);
            })
            ->unique()
            ->sort()
            ->values();

        // Get action types
        $actionTypes = ['created', 'updated', 'deleted', 'viewed', 'exported', 'imported', 'login', 'logout'];

        // Paginate results
        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.audit-logs.index', compact('logs', 'stats', 'modelTypes', 'actionTypes'));
    }

    /**
     * Display specific audit log
     */
    public function show(AuditLog $log)
    {
        $log->load('user');

        // Parse old and new values if they exist
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

    // Apply same filters as index
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
              ->orWhere('model_type', 'like', "%{$search}%")
              ->orWhere('ip_address', 'like', "%{$search}%")
              ->orWhereHas('user', function($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    if ($request->filled('action')) {
        $query->where('action', $request->action);
    }

    if ($request->filled('model')) {
        $query->where('model_type', $request->model);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // Get logs without pagination for print
    $logs = $query->orderBy('created_at', 'desc')->get();

    // Get stats
    $stats = $this->getStats($request);

    // Get action distribution for print
    $actionDistribution = AuditLog::select('action', DB::raw('count(*) as count'))
        ->groupBy('action')
        ->orderBy('count', 'desc')
        ->get();

    return view('admin.audit-logs.print', compact('logs', 'stats', 'actionDistribution'));
}

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $query = AuditLog::with('user');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model')) {
            $query->where('model_type', $request->model);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'audit-logs-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, ['ID', 'User', 'Action', 'Model', 'Description', 'IP Address', 'Date']);

            // Add data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user ? $log->user->name : 'System',
                    ucfirst($log->action),
                    $log->model_type ? class_basename($log->model_type) : 'N/A',
                    $log->description,
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get statistics
     */
    private function getStats($request)
    {
        $query = AuditLog::query();

        // Apply date filters if present
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return [
            'total_logs' => $query->count(),
            'today_logs' => (clone $query)->whereDate('created_at', today())->count(),
            'this_week_logs' => (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'actions_by_type' => AuditLog::select('action', DB::raw('count(*) as count'))
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray(),
        ];
    }
}
