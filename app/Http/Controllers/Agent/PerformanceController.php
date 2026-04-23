<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use App\Models\Vendor\Vendor;
use App\Models\Product;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $agentId = auth()->id();
        $period  = (int) $request->get('period', 6);

        // All vendor user_ids under this agent
        $agentVendors   = Vendor::where('agent_id', $agentId)->with('businessProfile')->get();
        $vendorUserIds  = $agentVendors->pluck('user_id');
        $vendorBpIds    = $agentVendors->pluck('business_profile_id'); // Performance.vendor_id = business_profile_id

        // ── Summary Totals ─────────────────────────────────────────────
        $summary = [
            'total_clicks'      => Performance::whereIn('vendor_id', $vendorBpIds)->sum('clicks'),
            'total_impressions' => Performance::whereIn('vendor_id', $vendorBpIds)->sum('impressions'),
            'total_products'    => Product::whereIn('user_id', $vendorUserIds)->count(),
            'active_vendors'    => $agentVendors->where('account_status', 'active')->count(),
        ];

        $summary['overall_ctr'] = $summary['total_impressions'] > 0
            ? round(($summary['total_clicks'] / $summary['total_impressions']) * 100, 2)
            : 0;

        // This month vs last month
        $summary['clicks_this_month'] = Performance::whereIn('vendor_id', $vendorBpIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('clicks');

        $summary['clicks_last_month'] = Performance::whereIn('vendor_id', $vendorBpIds)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('clicks');

        $summary['impressions_this_month'] = Performance::whereIn('vendor_id', $vendorBpIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('impressions');

        // ── Monthly Clicks + Impressions Chart ─────────────────────────
        $monthlyChart = Performance::whereIn('vendor_id', $vendorBpIds)
            ->where('created_at', '>=', now()->subMonths($period)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(impressions) as total_impressions')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Top Products by Clicks ─────────────────────────────────────
        $topProductsByClicks = Performance::whereIn('vendor_id', $vendorBpIds)
            ->select(
                'product_id',
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(impressions) as total_impressions')
            )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_clicks')
            ->limit(10)
            ->get();

        // ── Top Products by CTR ────────────────────────────────────────
        $topProductsByCtr = Performance::whereIn('vendor_id', $vendorBpIds)
            ->select(
                'product_id',
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('ROUND((SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100, 2) as ctr')
            )
            ->with('product')
            ->groupBy('product_id')
            ->having('total_impressions', '>', 0)
            ->orderByDesc('ctr')
            ->limit(10)
            ->get();

        // ── Per-Vendor Performance Breakdown ──────────────────────────
        $vendorBreakdown = Performance::whereIn('vendor_id', $vendorBpIds)
            ->select(
                'vendor_id',
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('ROUND((SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100, 2) as ctr'),
                DB::raw('COUNT(DISTINCT product_id) as product_count')
            )
            ->with('vendor')
            ->groupBy('vendor_id')
            ->orderByDesc('total_clicks')
            ->get();

        // ── By Country ────────────────────────────────────────────────
        $byCountry = Performance::whereIn('vendor_id', $vendorBpIds)
            ->select(
                'country_id',
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(impressions) as total_impressions')
            )
            ->with('country')
            ->groupBy('country_id')
            ->orderByDesc('total_clicks')
            ->limit(8)
            ->get();

        // ── Paginated Product Records ──────────────────────────────────
        $query = Performance::whereIn('vendor_id', $vendorBpIds)
            ->select(
                'product_id',
                'vendor_id',
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('ROUND((SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100, 2) as ctr'),
                DB::raw('MAX(created_at) as last_recorded')
            )
            ->with(['product', 'vendor'])
            ->groupBy('product_id', 'vendor_id')
            ->when($request->vendor_id, fn($q) =>
                $q->where('vendor_id', $request->vendor_id)
            )
            ->when($request->search, fn($q) =>
                $q->whereHas('product', fn($q2) =>
                    $q2->where('name', 'like', "%{$request->search}%")
                )
            )
            ->when($request->sort === 'impressions', fn($q) => $q->orderByDesc('total_impressions'))
            ->when($request->sort === 'ctr',         fn($q) => $q->orderByDesc('ctr'))
            ->when(!$request->sort || $request->sort === 'clicks', fn($q) => $q->orderByDesc('total_clicks'));

        $records = $query->paginate(15)->withQueryString();

        return view('agent.performance.index', compact(
            'summary', 'monthlyChart', 'topProductsByClicks', 'topProductsByCtr',
            'vendorBreakdown', 'byCountry', 'records', 'agentVendors', 'period'
        ));
    }

    // ─── PRINT ────────────────────────────────────────────────────────
    public function print(Request $request)
    {
        $agentId     = auth()->id();
        $vendorBpIds = Vendor::where('agent_id', $agentId)->pluck('business_profile_id');

        $summary = [
            'total_clicks'      => Performance::whereIn('vendor_id', $vendorBpIds)->sum('clicks'),
            'total_impressions' => Performance::whereIn('vendor_id', $vendorBpIds)->sum('impressions'),
        ];
        $summary['overall_ctr'] = $summary['total_impressions'] > 0
            ? round(($summary['total_clicks'] / $summary['total_impressions']) * 100, 2)
            : 0;

        $records = Performance::whereIn('vendor_id', $vendorBpIds)
            ->select(
                'product_id',
                'vendor_id',
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('ROUND((SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100, 2) as ctr')
            )
            ->with(['product', 'vendor'])
            ->groupBy('product_id', 'vendor_id')
            ->orderByDesc('total_clicks')
            ->get();

        $vendorBreakdown = Performance::whereIn('vendor_id', $vendorBpIds)
            ->select(
                'vendor_id',
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('ROUND((SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100, 2) as ctr')
            )
            ->with('vendor')
            ->groupBy('vendor_id')
            ->orderByDesc('total_clicks')
            ->get();

        return view('agent.performance.print', compact('summary', 'records', 'vendorBreakdown'));
    }

    // ─── SHOW single product performance ─────────────────────────────
    public function show(Request $request, $productId)
    {
        $agentId     = auth()->id();
        $vendorBpIds = Vendor::where('agent_id', $agentId)->pluck('business_profile_id');

        // Ensure this product belongs to one of the agent's vendors
        $product = Product::findOrFail($productId);

        $period = (int) $request->get('period', 6);

        // Aggregate totals for this product
        $totals = Performance::where('product_id', $productId)
            ->whereIn('vendor_id', $vendorBpIds)
            ->select(
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('ROUND((SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100, 2) as ctr')
            )
            ->first();

        // Monthly chart for this product
        $monthlyChart = Performance::where('product_id', $productId)
            ->whereIn('vendor_id', $vendorBpIds)
            ->where('created_at', '>=', now()->subMonths($period)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(impressions) as total_impressions')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // By country for this product
        $byCountry = Performance::where('product_id', $productId)
            ->whereIn('vendor_id', $vendorBpIds)
            ->select(
                'country_id',
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('SUM(impressions) as total_impressions')
            )
            ->with('country')
            ->groupBy('country_id')
            ->orderByDesc('total_clicks')
            ->get();

        return view('agent.performance.show', compact(
            'product', 'totals', 'monthlyChart', 'byCountry', 'period'
        ));
    }
}
