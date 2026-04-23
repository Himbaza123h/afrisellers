<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShowroomController extends Controller
{
    public function index(Request $request)
    {
        $query = Showroom::with(['user', 'country']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('showroom_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('business_type', 'like', "%{$search}%");
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

        // Business type filter
        if ($request->filled('business_type')) {
            $query->where('business_type', $request->business_type);
        }

        // Size filter
        if ($request->filled('size')) {
            switch ($request->size) {
                case 'large':
                    $query->where('showroom_size_sqm', '>=', 1000);
                    break;
                case 'medium':
                    $query->whereBetween('showroom_size_sqm', [500, 999]);
                    break;
                case 'small':
                    $query->where('showroom_size_sqm', '<', 500);
                    break;
            }
        }

        // Rating filter
        if ($request->filled('rating')) {
            switch ($request->rating) {
                case '4plus':
                    $query->where('rating', '>=', 4);
                    break;
                case '3plus':
                    $query->where('rating', '>=', 3);
                    break;
                case 'below3':
                    $query->where('rating', '<', 3);
                    break;
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

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $showrooms = $query->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = $this->getShowroomStats();

        // Add status badge to each showroom
        $showrooms->getCollection()->transform(function ($showroom) {
            $showroom->status_badge = $this->getStatusBadge($showroom->status);
            return $showroom;
        });

        $countries = Country::orderBy('name')->get();

        return view('admin.showrooms.index', compact('showrooms', 'stats', 'countries'));
    }

    /**
     * Print showrooms report
     */
    public function print()
    {
        $showrooms = Showroom::with(['country'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = $this->getShowroomStats();

        return view('admin.showrooms.print', compact('showrooms', 'stats'));
    }

    /**
     * Get showroom statistics
     */
    private function getShowroomStats()
    {
        $total = Showroom::count();
        $active = Showroom::where('status', 'active')->count();
        $pending = Showroom::where('status', 'pending')->count();
        $suspended = Showroom::where('status', 'suspended')->count();
        $inactive = Showroom::where('status', 'inactive')->count();
        $verified = Showroom::where('is_verified', true)->count();
        $featured = Showroom::where('is_featured', true)->count();

        $totalViews = Showroom::sum('views_count');
        $totalInquiries = Showroom::sum('inquiries_count');
        $avgRating = Showroom::avg('rating');

        $today = Showroom::whereDate('created_at', today())->count();
        $thisWeek = Showroom::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonth = Showroom::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $stats = [
            'total' => $total,
            'active' => $active,
            'pending' => $pending,
            'suspended' => $suspended,
            'inactive' => $inactive,
            'verified' => $verified,
            'featured' => $featured,
            'total_views' => $totalViews,
            'total_inquiries' => $totalInquiries,
            'avg_rating' => number_format($avgRating ?? 0, 1),
            'today' => $today,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth,
        ];

        // Calculate percentages
        if ($total > 0) {
            $stats['active_percentage'] = round(($active / $total) * 100, 1);
            $stats['verified_percentage'] = round(($verified / $total) * 100, 1);
            $stats['featured_percentage'] = round(($featured / $total) * 100, 1);
            $stats['pending_percentage'] = round(($pending / $total) * 100, 1);
        } else {
            $stats['active_percentage'] = 0;
            $stats['verified_percentage'] = 0;
            $stats['featured_percentage'] = 0;
            $stats['pending_percentage'] = 0;
        }

        return $stats;
    }

    public function show(Showroom $showroom)
    {
        $showroom->load(['user', 'country', 'products']);
        return view('admin.showrooms.show', compact('showroom'));
    }

    public function verify(Showroom $showroom)
    {
        $showroom->update(['is_verified' => true]);
        return redirect()->back()->with('success', 'Showroom verified successfully');
    }

    public function unverify(Showroom $showroom)
    {
        $showroom->update(['is_verified' => false]);
        return redirect()->back()->with('success', 'Verification revoked successfully');
    }

    public function feature(Showroom $showroom)
    {
        $showroom->update(['is_featured' => !$showroom->is_featured]);
        $message = $showroom->is_featured ? 'Showroom featured successfully' : 'Showroom unfeatured successfully';
        return redirect()->back()->with('success', $message);
    }

    public function activate(Showroom $showroom)
    {
        $showroom->update(['status' => 'active']);
        return redirect()->back()->with('success', 'Showroom activated successfully');
    }

    public function suspend(Showroom $showroom)
    {
        $showroom->update(['status' => 'suspended']);
        return redirect()->back()->with('success', 'Showroom suspended successfully');
    }

    public function destroy(Showroom $showroom)
    {
        $showroom->delete();
        return redirect()->route('admin.showrooms.index')->with('success', 'Showroom deleted successfully');
    }

    private function getStatusBadge($status)
    {
        return match($status) {
            'active' => ['text' => 'Active', 'class' => 'bg-green-100 text-green-800'],
            'pending' => ['text' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-800'],
            'suspended' => ['text' => 'Suspended', 'class' => 'bg-red-100 text-red-800'],
            'inactive' => ['text' => 'Inactive', 'class' => 'bg-gray-100 text-gray-800'],
            default => ['text' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'],
        };
    }
}
