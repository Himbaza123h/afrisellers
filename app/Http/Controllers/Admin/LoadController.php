<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Load;
use App\Models\Country;
use Illuminate\Http\Request;

class LoadController extends Controller
{
    public function index(Request $request)
    {
        $query = Load::with(['user', 'originCountry', 'destinationCountry', 'assignedTransporter']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('load_number', 'like', "%{$search}%")
                  ->orWhere('cargo_type', 'like', "%{$search}%")
                  ->orWhere('origin_city', 'like', "%{$search}%")
                  ->orWhere('destination_city', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('filter')) {
            $query->where('status', $request->filter);
        }

        // Origin country filter
        if ($request->filled('origin_country')) {
            $query->where('origin_country_id', $request->origin_country);
        }

        // Destination country filter
        if ($request->filled('destination_country')) {
            $query->where('destination_country_id', $request->destination_country);
        }

        // Cargo type filter
        if ($request->filled('cargo_type')) {
            $query->where('cargo_type', $request->cargo_type);
        }

        // Weight range filter
        if ($request->filled('weight_range')) {
            switch ($request->weight_range) {
                case 'heavy':
                    $query->where('weight', '>=', 10000);
                    break;
                case 'medium':
                    $query->whereBetween('weight', [1000, 9999]);
                    break;
                case 'light':
                    $query->where('weight', '<', 1000);
                    break;
            }
        }

        // Assignment filter
        if ($request->filled('assignment')) {
            if ($request->assignment === 'assigned') {
                $query->whereNotNull('assigned_transporter_id');
            } else {
                $query->whereNull('assigned_transporter_id');
            }
        }

        // Date range filter
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Pickup date filter
        if ($request->filled('pickup_date')) {
            switch ($request->pickup_date) {
                case 'upcoming':
                    $query->where('pickup_date', '>', now());
                    break;
                case 'overdue':
                    $query->where('pickup_date', '<', now())
                          ->whereIn('status', ['posted', 'bidding', 'assigned']);
                    break;
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $loads = $query->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => Load::count(),
            'posted' => Load::where('status', 'posted')->count(),
            'in_transit' => Load::where('status', 'in_transit')->count(),
            'delivered' => Load::where('status', 'delivered')->count(),
            'cancelled' => Load::where('status', 'cancelled')->count(),
            'total_bids' => Load::withCount('bids')->get()->sum('bids_count'),
            'avg_bids' => number_format(Load::withCount('bids')->get()->avg('bids_count'), 1),
            'total_weight' => number_format(Load::sum('weight'), 0),
            'this_month' => Load::whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)
                               ->count(),
        ];

        $stats['in_transit_percentage'] = $stats['total'] > 0
            ? round(($stats['in_transit'] / $stats['total']) * 100, 1)
            : 0;
        $stats['delivered_percentage'] = $stats['total'] > 0
            ? round(($stats['delivered'] / $stats['total']) * 100, 1)
            : 0;

        // Add status badges
        $loads->getCollection()->transform(function ($load) {
            $load->status_badge = $this->getStatusBadge($load->status);
            $load->urgency_badge = $this->getUrgencyBadge($load);
            return $load;
        });

        $countries = Country::orderBy('name')->get();

        return view('admin.loads.index', compact('loads', 'stats', 'countries'));
    }

    public function show(Load $load)
    {
        $load->load(['user', 'originCountry', 'destinationCountry', 'assignedTransporter', 'bids.transporter']);
        return view('admin.loads.show', compact('load'));
    }

    public function cancel(Load $load, Request $request)
    {
        $load->cancel($request->reason);
        return redirect()->back()->with('success', 'Load cancelled successfully');
    }

    public function destroy(Load $load)
    {
        $load->delete();
        return redirect()->route('admin.loads.index')->with('success', 'Load deleted successfully');
    }

    private function getStatusBadge($status)
    {
        return match($status) {
            'posted' => ['text' => 'Posted', 'class' => 'bg-blue-100 text-blue-800'],
            'bidding' => ['text' => 'Bidding', 'class' => 'bg-purple-100 text-purple-800'],
            'assigned' => ['text' => 'Assigned', 'class' => 'bg-yellow-100 text-yellow-800'],
            'in_transit' => ['text' => 'In Transit', 'class' => 'bg-orange-100 text-orange-800'],
            'delivered' => ['text' => 'Delivered', 'class' => 'bg-green-100 text-green-800'],
            'cancelled' => ['text' => 'Cancelled', 'class' => 'bg-red-100 text-red-800'],
            default => ['text' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'],
        };
    }

    private function getUrgencyBadge($load)
    {
        if (!$load->pickup_date) {
            return ['text' => 'Not Set', 'class' => 'bg-gray-100 text-gray-800'];
        }

        $daysUntilPickup = now()->diffInDays($load->pickup_date, false);

        if ($daysUntilPickup < 0) {
            return ['text' => 'Overdue', 'class' => 'bg-red-100 text-red-800'];
        } elseif ($daysUntilPickup <= 2) {
            return ['text' => 'Urgent', 'class' => 'bg-orange-100 text-orange-800'];
        } elseif ($daysUntilPickup <= 7) {
            return ['text' => 'Soon', 'class' => 'bg-yellow-100 text-yellow-800'];
        } else {
            return ['text' => 'Scheduled', 'class' => 'bg-green-100 text-green-800'];
        }
    }

    public function print()
{
    // Calculate statistics using separate queries for better performance
    $total = Load::count();
    $posted = Load::where('status', 'posted')->count();
    $inTransit = Load::where('status', 'in_transit')->count();
    $delivered = Load::where('status', 'delivered')->count();
    $cancelled = Load::where('status', 'cancelled')->count();
    $totalBids = Load::withCount('bids')->get()->sum('bids_count');
    $avgBids = Load::withCount('bids')->get()->avg('bids_count');
    $totalWeight = Load::sum('weight');
    $thisMonth = Load::whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year)
                     ->count();

    $stats = [
        'total' => $total,
        'posted' => $posted,
        'in_transit' => $inTransit,
        'delivered' => $delivered,
        'cancelled' => $cancelled,
        'total_bids' => $totalBids,
        'avg_bids' => $avgBids ? number_format($avgBids, 1) : '0.0',
        'total_weight' => number_format($totalWeight, 0),
        'this_month' => $thisMonth,
    ];

    $stats['in_transit_percentage'] = $stats['total'] > 0
        ? round(($stats['in_transit'] / $stats['total']) * 100, 1)
        : 0;
    $stats['delivered_percentage'] = $stats['total'] > 0
        ? round(($stats['delivered'] / $stats['total']) * 100, 1)
        : 0;

    // Get all loads for print (no pagination)
    $loads = Load::with(['user', 'originCountry', 'destinationCountry', 'assignedTransporter'])
        ->latest()
        ->get();

    return view('admin.loads.print', compact('loads', 'stats'));
}
}
