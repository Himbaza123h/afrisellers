<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tradeshow;
use App\Models\Country;
use Illuminate\Http\Request;

class TradeshowController extends Controller
{
    public function index(Request $request)
    {
        $query = Tradeshow::with(['user', 'country']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('tradeshow_number', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('venue_name', 'like', "%{$search}%")
                  ->orWhere('industry', 'like', "%{$search}%")
                  ->orWhere('organizer_name', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('filter')) {
            $query->where('status', $request->filter);
        }

        // Country filter
        if ($request->filled('country')) {
            $query->where('country_id', $request->country);
        }

        // Verification filter
        if ($request->filled('verification')) {
            $query->where('is_verified', $request->verification);
        }

        // Featured filter
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured);
        }

        // Event status filter
        if ($request->filled('event_status')) {
            $now = now();
            switch ($request->event_status) {
                case 'upcoming':
                    $query->where('start_date', '>', $now);
                    break;
                case 'ongoing':
                    $query->where('start_date', '<=', $now)
                          ->where('end_date', '>=', $now);
                    break;
                case 'completed':
                    $query->where('end_date', '<', $now);
                    break;
            }
        }

        // Size filter (by expected visitors)
        if ($request->filled('size')) {
            switch ($request->size) {
                case 'large':
                    $query->where('expected_visitors', '>=', 10000);
                    break;
                case 'medium':
                    $query->whereBetween('expected_visitors', [1000, 9999]);
                    break;
                case 'small':
                    $query->where('expected_visitors', '<', 1000);
                    break;
            }
        }

        // Date range filter
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'this_month':
                    $query->whereMonth('start_date', now()->month)
                          ->whereYear('start_date', now()->year);
                    break;
                case 'next_month':
                    $query->whereMonth('start_date', now()->addMonth()->month)
                          ->whereYear('start_date', now()->addMonth()->year);
                    break;
                case 'this_quarter':
                    $query->whereBetween('start_date', [now()->startOfQuarter(), now()->endOfQuarter()]);
                    break;
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'start_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tradeshows = $query->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => Tradeshow::count(),
            'upcoming' => Tradeshow::where('start_date', '>', now())->count(),
            'ongoing' => Tradeshow::where('start_date', '<=', now())
                                  ->where('end_date', '>=', now())
                                  ->count(),
            'completed' => Tradeshow::where('end_date', '<', now())->count(),
            'verified' => Tradeshow::where('is_verified', true)->count(),
            'featured' => Tradeshow::where('is_featured', true)->count(),
            'total_expected_visitors' => Tradeshow::sum('expected_visitors'),
            'avg_visitors' => number_format(Tradeshow::avg('expected_visitors'), 0),
            'this_month' => Tradeshow::whereMonth('start_date', now()->month)
                                     ->whereYear('start_date', now()->year)
                                     ->count(),
        ];

        $stats['upcoming_percentage'] = $stats['total'] > 0
            ? round(($stats['upcoming'] / $stats['total']) * 100, 1)
            : 0;
        $stats['verified_percentage'] = $stats['total'] > 0
            ? round(($stats['verified'] / $stats['total']) * 100, 1)
            : 0;

        // Add status badges
        $tradeshows->getCollection()->transform(function ($tradeshow) {
            $tradeshow->status_badge = $this->getStatusBadge($tradeshow->status);
            $tradeshow->event_status_badge = $this->getEventStatusBadge($tradeshow);
            return $tradeshow;
        });

        $countries = Country::orderBy('name')->get();

        return view('admin.tradeshows.index', compact('tradeshows', 'stats', 'countries'));
    }

    public function show(Tradeshow $tradeshow)
    {
        $tradeshow->load(['user', 'country']);
        return view('admin.tradeshows.show', compact('tradeshow'));
    }

    public function approve(Tradeshow $tradeshow)
    {
        $tradeshow->update(['status' => 'published']);
        return redirect()->back()->with('success', 'Tradeshow approved successfully');
    }

    public function verify(Tradeshow $tradeshow)
    {
        $tradeshow->update(['is_verified' => true]);
        return redirect()->back()->with('success', 'Tradeshow verified successfully');
    }

    public function unverify(Tradeshow $tradeshow)
    {
        $tradeshow->update(['is_verified' => false]);
        return redirect()->back()->with('success', 'Verification revoked successfully');
    }

    public function feature(Tradeshow $tradeshow)
    {
        $tradeshow->update(['is_featured' => !$tradeshow->is_featured]);
        $message = $tradeshow->is_featured ? 'Tradeshow featured successfully' : 'Tradeshow unfeatured successfully';
        return redirect()->back()->with('success', $message);
    }

    public function suspend(Tradeshow $tradeshow)
    {
        $tradeshow->update(['status' => 'suspended']);
        return redirect()->back()->with('success', 'Tradeshow suspended successfully');
    }

    public function destroy(Tradeshow $tradeshow)
    {
        $tradeshow->delete();
        return redirect()->route('admin.tradeshows.index')->with('success', 'Tradeshow deleted successfully');
    }

    private function getStatusBadge($status)
    {
        return match($status) {
            'published' => ['text' => 'Published', 'class' => 'bg-green-100 text-green-800'],
            'draft' => ['text' => 'Draft', 'class' => 'bg-gray-100 text-gray-800'],
            'pending' => ['text' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-800'],
            'suspended' => ['text' => 'Suspended', 'class' => 'bg-red-100 text-red-800'],
            default => ['text' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'],
        };
    }

    private function getEventStatusBadge($tradeshow)
    {
        $now = now();

        if ($tradeshow->start_date > $now) {
            return ['text' => 'Upcoming', 'class' => 'bg-blue-100 text-blue-800'];
        } elseif ($tradeshow->start_date <= $now && $tradeshow->end_date >= $now) {
            return ['text' => 'Ongoing', 'class' => 'bg-emerald-100 text-emerald-800'];
        } else {
            return ['text' => 'Completed', 'class' => 'bg-gray-100 text-gray-800'];
        }
    }
}
