<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\RFQs;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get real data from database
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Global Revenue - Sum of all orders this month
        $globalRevenue = Order::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total') ?? 0;

        // Active Vendors - Users with vendor role
        $activeVendors = User::whereHas('roles', function($q) {
            $q->where('slug', 'vendor');
        })->count();

        // Total Orders this month
        $totalOrders = Order::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Pending Approvals - Real counts from database
        $pendingProducts = Product::where('status', 'pending')->count();
        $pendingRFQs = RFQs::where('status', 'pending')->count();
        $pendingApprovals = $pendingProducts + $pendingRFQs;

        // Recent Activities
        $recentActivities = $this->getRecentActivities();

        // Regional Performance Data for Chart
        $regionalData = $this->getRegionalPerformance();

        // Regional Statistics
        $regionalStats = $this->getRegionalStatistics();

        // User Hierarchy Overview
        $userHierarchy = $this->getUserHierarchy();

        return view('admin.dashboard.index', compact(
            'globalRevenue',
            'activeVendors',
            'totalOrders',
            'pendingApprovals',
            'recentActivities',
            'regionalData',
            'regionalStats',
            'userHierarchy'
        ));
    }

    private function getRecentActivities()
    {
        $activities = [];

        // Get pending products
        $pendingProducts = Product::where('status', 'pending')
            ->with('user')
            ->latest()
            ->take(3)
            ->get();

        foreach ($pendingProducts as $product) {
            $activities[] = [
                'title' => 'Product Approval Required',
                'description' => ($product->name ?? 'Product') . ' by ' . ($product->user->name ?? 'Unknown'),
                'color' => 'purple',
                'icon' => 'box',
                'actions' => [
                    ['label' => 'Approve', 'icon' => 'check', 'color' => 'green'],
                    ['label' => 'Reject', 'icon' => 'times', 'color' => 'red']
                ]
            ];
        }

        // Get pending RFQs
        $pendingRFQs = RFQs::where('status', 'pending')
            ->with('user')
            ->latest()
            ->take(2)
            ->get();

        foreach ($pendingRFQs as $rfq) {
            $activities[] = [
                'title' => 'New RFQ Submitted',
                'description' => ($rfq->product_name ?? 'RFQ') . ' by ' . ($rfq->user->name ?? 'Unknown'),
                'color' => 'blue',
                'icon' => 'file-invoice',
                'actions' => [
                    ['label' => 'Review', 'icon' => 'eye', 'color' => 'blue'],
                    ['label' => 'Approve', 'icon' => 'check', 'color' => 'green']
                ]
            ];
        }

        return collect($activities)->take(5);
    }

    private function getRegionalPerformance()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get all regions with their data
        $regions = Region::active()
            ->with('countries')
            ->get()
            ->map(function($region) use ($currentMonth, $currentYear) {
                $countryIds = $region->countries->pluck('id');

                // Get vendor user IDs for this region through business_profiles
                $vendorUserIds = DB::table('vendors')
                    ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
                    ->whereIn('business_profiles.country_id', $countryIds)
                    ->whereNull('vendors.deleted_at')
                    ->pluck('vendors.user_id');

                // Get revenue for this region
                $revenue = Order::whereIn('vendor_id', $vendorUserIds)
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->sum('total') ?? 0;

                // Get vendor count
                $vendorCount = User::whereHas('roles', function($q) {
                    $q->where('slug', 'vendor');
                })->whereHas('vendor.businessProfile', function($q) use ($countryIds) {
                    $q->whereIn('country_id', $countryIds);
                })->count();

                // Get order count
                $orderCount = Order::whereIn('vendor_id', $vendorUserIds)
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->count();

                return [
                    'region' => $region->name,
                    'revenue' => $revenue / 1000, // in thousands
                    'vendors' => $vendorCount,
                    'orders' => $orderCount
                ];
            })
            ->keyBy('region');

        return $regions;
    }

    private function getRegionalStatistics()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get all regions from database
        $regions = Region::active()->with('countries')->get();

        $stats = [];

        foreach ($regions as $region) {
            $countryIds = $region->countries->pluck('id');

            // Get vendor user IDs for this region through business_profiles
            $vendorUserIds = DB::table('vendors')
                ->join('business_profiles', 'vendors.business_profile_id', '=', 'business_profiles.id')
                ->whereIn('business_profiles.country_id', $countryIds)
                ->whereNull('vendors.deleted_at')
                ->pluck('vendors.user_id');

            // Calculate revenue for region
            $revenue = Order::whereIn('vendor_id', $vendorUserIds)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('total') ?? 0;

            // Count vendors in region
            $vendorCount = User::whereHas('roles', function($q) {
                $q->where('slug', 'vendor');
            })->whereHas('vendor.businessProfile', function($q) use ($countryIds) {
                $q->whereIn('country_id', $countryIds);
            })->count();

            // Count orders in region
            $orderCount = Order::whereIn('vendor_id', $vendorUserIds)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();

            $stats[] = [
                'name' => $region->name,
                'revenue' => $revenue,
                'percentage' => $revenue > 0 ? min(100, ($revenue / 10000)) : 0,
                'color' => $this->getRegionColor($region->name),
                'vendors' => $vendorCount,
                'orders' => $orderCount,
                'status' => $vendorCount > 1000 ? 'Active' : ($vendorCount > 0 ? 'Growing' : 'Inactive'),
                'badge_color' => $vendorCount > 1000 ? null : ($vendorCount > 0 ? 'yellow' : 'gray')
            ];
        }

        return $stats;
    }

    private function getUserHierarchy()
    {
        // Get actual counts from database for each role
        return [
            [
                'role' => 'Administrators',
                'icon' => 'user-shield',
                'color' => 'red',
                'total' => User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->count(),
                'active' => User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))
                    ->whereNotNull('email_verified_at')->count(),
                'pending' => User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))
                    ->whereNull('email_verified_at')->count(),
                'suspended' => 0,
            ],
            [
                'role' => 'Vendors',
                'icon' => 'store',
                'color' => 'purple',
                'total' => User::whereHas('roles', fn($q) => $q->where('slug', 'vendor'))->count(),
                'active' => User::whereHas('roles', fn($q) => $q->where('slug', 'vendor'))
                    ->whereNotNull('email_verified_at')->count(),
                'pending' => User::whereHas('roles', fn($q) => $q->where('slug', 'vendor'))
                    ->whereNull('email_verified_at')->count(),
                'suspended' => 0,
            ],
            [
                'role' => 'Buyers',
                'icon' => 'users',
                'color' => 'green',
                'total' => User::whereHas('roles', fn($q) => $q->where('slug', 'buyer'))->count(),
                'active' => User::whereHas('roles', fn($q) => $q->where('slug', 'buyer'))
                    ->whereNotNull('email_verified_at')->count(),
                'pending' => User::whereHas('roles', fn($q) => $q->where('slug', 'buyer'))
                    ->whereNull('email_verified_at')->count(),
                'suspended' => 0,
            ],
            [
                'role' => 'Transporters',
                'icon' => 'truck',
                'color' => 'blue',
                'total' => User::whereHas('roles', fn($q) => $q->where('slug', 'transporter'))->count(),
                'active' => User::whereHas('roles', fn($q) => $q->where('slug', 'transporter'))
                    ->whereNotNull('email_verified_at')->count(),
                'pending' => User::whereHas('roles', fn($q) => $q->where('slug', 'transporter'))
                    ->whereNull('email_verified_at')->count(),
                'suspended' => 0,
            ],
            [
                'role' => 'Agents',
                'icon' => 'handshake',
                'color' => 'orange',
                'total' => User::whereHas('roles', fn($q) => $q->where('slug', 'agent'))->count(),
                'active' => User::whereHas('roles', fn($q) => $q->where('slug', 'agent'))
                    ->whereNotNull('email_verified_at')->count(),
                'pending' => User::whereHas('roles', fn($q) => $q->where('slug', 'agent'))
                    ->whereNull('email_verified_at')->count(),
                'suspended' => 0,
            ],
        ];
    }

    private function getRegionColor($regionName)
    {
        return match($regionName) {
            'East Africa' => 'green',
            'West Africa' => 'blue',
            'Southern Africa' => 'purple',
            'North Africa' => 'orange',
            'Central Africa' => 'indigo',
            default => 'gray'
        };
    }
}
