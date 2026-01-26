<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transporter;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class TransporterController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Transporter::with(['user', 'country']);

            // Filter by status
            $filter = $request->get('filter', '');
            if ($filter === 'active') {
                $query->where('status', 'active');
            } elseif ($filter === 'inactive') {
                $query->where('status', 'inactive');
            } elseif ($filter === 'suspended') {
                $query->where('status', 'suspended');
            }

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                      ->orWhere('registration_number', 'like', "%{$search}%")
                      ->orWhere('license_number', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            // Country filter
            if ($request->filled('country')) {
                $query->where('country_id', $request->country);
            }

            // Verification filter
            if ($request->filled('verification')) {
                $query->where('is_verified', $request->verification);
            }

            // Rating filter
            if ($request->filled('rating')) {
                $rating = $request->rating;
                if ($rating === '4plus') {
                    $query->where('average_rating', '>=', 4);
                } elseif ($rating === '3plus') {
                    $query->where('average_rating', '>=', 3);
                } elseif ($rating === 'below3') {
                    $query->where('average_rating', '<', 3);
                }
            }

            // Fleet size filter
            if ($request->filled('fleet_size')) {
                $size = $request->fleet_size;
                if ($size === 'large') {
                    $query->where('fleet_size', '>=', 20);
                } elseif ($size === 'medium') {
                    $query->whereBetween('fleet_size', [5, 19]);
                } elseif ($size === 'small') {
                    $query->where('fleet_size', '<', 5);
                }
            }

            // Date range filter
            if ($request->filled('date_range')) {
                $dateRange = $request->date_range;
                if ($dateRange === 'today') {
                    $query->whereDate('created_at', today());
                } elseif ($dateRange === 'week') {
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($dateRange === 'month') {
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                }
            }

            // Handle sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            switch ($sortBy) {
                case 'company_name':
                    $query->orderBy('company_name', $sortOrder);
                    break;
                case 'average_rating':
                    $query->orderBy('average_rating', $sortOrder);
                    break;
                case 'total_deliveries':
                    $query->orderBy('total_deliveries', $sortOrder);
                    break;
                case 'fleet_size':
                    $query->orderBy('fleet_size', $sortOrder);
                    break;
                case 'status':
                    $query->orderBy('status', $sortOrder);
                    break;
                default:
                    $query->orderBy('created_at', $sortOrder);
            }

            $transporters = $query->paginate(15)->withQueryString();

            // Get all countries for filter dropdown
            $countries = Country::orderBy('name')->get();

            // Calculate statistics
            $total = Transporter::count();
            $active = Transporter::where('status', 'active')->count();
            $inactive = Transporter::where('status', 'inactive')->count();
            $suspended = Transporter::where('status', 'suspended')->count();
            $verified = Transporter::where('is_verified', true)->count();
            $unverified = Transporter::where('is_verified', false)->count();

            // Performance stats
            $totalDeliveries = Transporter::sum('total_deliveries');
            $successfulDeliveries = Transporter::sum('successful_deliveries');
            $totalFleetSize = Transporter::sum('fleet_size');
            $avgRating = Transporter::avg('average_rating');
            $successRate = $totalDeliveries > 0
                ? round(($successfulDeliveries / $totalDeliveries) * 100, 1)
                : 0;

            // Rating distribution
            $excellentRating = Transporter::where('average_rating', '>=', 4)->count();
            $goodRating = Transporter::whereBetween('average_rating', [3, 3.99])->count();
            $poorRating = Transporter::where('average_rating', '<', 3)->count();

            // Time-based stats
            $today = Transporter::whereDate('created_at', today())->count();
            $thisWeek = Transporter::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count();
            $thisMonth = Transporter::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $stats = [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'suspended' => $suspended,
                'verified' => $verified,
                'unverified' => $unverified,
                'total_deliveries' => $totalDeliveries,
                'successful_deliveries' => $successfulDeliveries,
                'total_fleet_size' => $totalFleetSize,
                'avg_rating' => round($avgRating, 2),
                'success_rate' => $successRate,
                'excellent_rating' => $excellentRating,
                'good_rating' => $goodRating,
                'poor_rating' => $poorRating,
                'active_percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
                'inactive_percentage' => $total > 0 ? round(($inactive / $total) * 100, 1) : 0,
                'suspended_percentage' => $total > 0 ? round(($suspended / $total) * 100, 1) : 0,
                'verified_percentage' => $total > 0 ? round(($verified / $total) * 100, 1) : 0,
                'today' => $today,
                'this_week' => $thisWeek,
                'this_month' => $thisMonth,
            ];

            return view('admin.transporter.index', compact(
                'transporters',
                'countries',
                'stats'
            ));
        } catch (\Exception $e) {
            Log::error('Admin Transporter Index Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('admin.dashboard.home')
                ->with('error', 'An error occurred while loading transporters.');
        }
    }

    public function print()
{
    $transporters = Transporter::with(['user', 'country'])->get();

    $total = Transporter::count();
    $active = Transporter::where('status', 'active')->count();
    $inactive = Transporter::where('status', 'inactive')->count();
    $suspended = Transporter::where('status', 'suspended')->count();
    $verified = Transporter::where('is_verified', true)->count();
    $unverified = Transporter::where('is_verified', false)->count();
    $totalDeliveries = Transporter::sum('total_deliveries');
    $successfulDeliveries = Transporter::sum('successful_deliveries');
    $totalFleetSize = Transporter::sum('fleet_size');
    $avgRating = Transporter::avg('average_rating');
    $successRate = $totalDeliveries > 0 ? round(($successfulDeliveries / $totalDeliveries) * 100, 1) : 0;

    $today = Transporter::whereDate('created_at', today())->count();
    $thisWeek = Transporter::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
    $thisMonth = Transporter::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

    $stats = [
        'total' => $total,
        'active' => $active,
        'inactive' => $inactive,
        'suspended' => $suspended,
        'verified' => $verified,
        'unverified' => $unverified,
        'total_deliveries' => $totalDeliveries,
        'successful_deliveries' => $successfulDeliveries,
        'total_fleet_size' => $totalFleetSize,
        'avg_rating' => round($avgRating, 2),
        'success_rate' => $successRate,
        'active_percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
        'suspended_percentage' => $total > 0 ? round(($suspended / $total) * 100, 1) : 0,
        'verified_percentage' => $total > 0 ? round(($verified / $total) * 100, 1) : 0,
        'today' => $today,
        'this_week' => $thisWeek,
        'this_month' => $thisMonth,
    ];

    return view('admin.transporter.print', compact('transporters', 'stats'));
}

    public function show(Transporter $transporter)
    {
        $transporter->load(['user', 'country']);

        return view('admin.transporter.show', compact('transporter'));
    }

    public function verify(Transporter $transporter)
    {
        try {
            $transporter->verify();

            return redirect()->back()->with('success', 'Transporter verified successfully.');
        } catch (\Exception $e) {
            Log::error('Transporter Verification Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to verify transporter.');
        }
    }

    public function unverify(Transporter $transporter)
    {
        try {
            $transporter->unverify();

            return redirect()->back()->with('success', 'Transporter verification revoked.');
        } catch (\Exception $e) {
            Log::error('Transporter Unverification Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to revoke verification.');
        }
    }

    public function activate(Transporter $transporter)
    {
        try {
            $transporter->activate();

            return redirect()->back()->with('success', 'Transporter activated successfully.');
        } catch (\Exception $e) {
            Log::error('Transporter Activation Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to activate transporter.');
        }
    }

    public function suspend(Transporter $transporter)
    {
        try {
            $transporter->suspend();

            return redirect()->back()->with('success', 'Transporter suspended successfully.');
        } catch (\Exception $e) {
            Log::error('Transporter Suspension Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to suspend transporter.');
        }
    }

    public function destroy(Transporter $transporter)
    {
        try {
            $transporter->delete();

            return redirect()->route('admin.transporters.index')
                ->with('success', 'Transporter deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Transporter Deletion Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete transporter.');
        }
    }
}
