<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Vendor;
use App\Models\RFQs;
use App\Models\RFQMessage;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class RFQController extends Controller
{
    /**
     * Get the authenticated vendor.
     */
    private function getVendor()
    {
        $vendor = Vendor::with(['plan', 'businessProfile'])->where('user_id', auth()->id())->first();

        if (!$vendor) {
            abort(403, 'Vendor profile not found.');
        }

        return $vendor;
    }

    /**
     * Display a listing of RFQs with priority ordering.
     */

/**
 * Display a listing of RFQs with priority ordering and filters.
 */
public function index(Request $request)
{
    try {
        $vendor = $this->getVendor();

        // Check if vendor has a plan
        if (!$vendor->plan_id || !$vendor->plan) {
            return redirect()->route('vendor.dashboard.home')
                ->with('error', 'You need to have a plan assigned to view RFQs. Please contact support.');
        }

        // Get the RFQ limit from plan (default to 5 if not set)
        $rfqLimit = $vendor->plan->buyer_rfqs_limit ?? 5;

        // Get vendor's country from business profile
        $vendorCountryId = $vendor->businessProfile->country_id ?? null;

        // Get vendor's product IDs (for direct product matching)
        $vendorProductIds = Product::where('user_id', $vendor->user_id)
            ->where('status', 'active')
            ->where('is_admin_verified', true)
            ->pluck('id')
            ->unique()
            ->toArray();

        // Get vendor's product categories from their products
        $vendorProductCategories = Product::where('user_id', $vendor->user_id)
            ->whereNotNull('product_category_id')
            ->pluck('product_category_id')
            ->unique()
            ->toArray();

        // Get vendor's business profile ID
        $vendorBusinessId = $vendor->business_profile_id ?? null;

        // Build prioritized RFQ query
        $rfqQuery = RFQs::with(['product', 'category', 'businessProfile', 'country'])
            ->withCount('messages');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $rfqQuery->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $rfqQuery->where('status', $request->status);
        }

        // Apply country filter
        if ($request->filled('country')) {
            $rfqQuery->where('country_id', $request->country);
        }

        // Date range filter (using flatpickr format)
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) === 2) {
                $rfqQuery->whereDate('created_at', '>=', $dates[0])
                          ->whereDate('created_at', '<=', $dates[1]);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'priority');
        $sortOrder = $request->get('sort_order', 'asc');

        // Build comprehensive priority ordering
        $priorityCases = [];

        if ($vendorCountryId) {
            $countryCondition = "country_id = {$vendorCountryId}";
            $productCondition = !empty($vendorProductIds)
                ? "product_id IN (" . implode(',', $vendorProductIds) . ")"
                : null;
            $categoryCondition = !empty($vendorProductCategories)
                ? "category_id IN (" . implode(',', $vendorProductCategories) . ")"
                : null;
            $businessCondition = $vendorBusinessId
                ? "business_id = {$vendorBusinessId}"
                : null;

            // Priority 1: Country + Product match (HIGHEST PRIORITY)
            if ($productCondition) {
                $priorityCases[] = "WHEN {$countryCondition} AND {$productCondition} THEN 1";
            }

            // Priority 2: Country + Category + Business match
            if ($categoryCondition && $businessCondition) {
                $priorityCases[] = "WHEN {$countryCondition} AND {$categoryCondition} AND {$businessCondition} THEN 2";
            }

            // Priority 3: Country + Category match
            if ($categoryCondition) {
                $priorityCases[] = "WHEN {$countryCondition} AND {$categoryCondition} THEN 3";
            }

            // Priority 4: Country + Business match
            if ($businessCondition) {
                $priorityCases[] = "WHEN {$countryCondition} AND {$businessCondition} THEN 4";
            }

            // Priority 5: Country match only
            $priorityCases[] = "WHEN {$countryCondition} THEN 5";
        }

        // Priority 6: Others
        $priorityCases[] = "ELSE 6";

        $priorityOrder = "CASE " . implode(' ', $priorityCases) . " END";

        // Apply sorting
        if ($sortBy === 'priority') {
            $rfqQuery->orderByRaw($priorityOrder);
            if ($sortOrder === 'desc') {
                $rfqQuery->latest('created_at');
            } else {
                $rfqQuery->oldest('created_at');
            }
        } else {
            $rfqQuery->orderBy($sortBy, $sortOrder);
        }

        // Get RFQs with limit and pagination
        $rfqs = $rfqQuery
            ->limit($rfqLimit)
            ->paginate(15)
            ->appends($request->query());

        // Statistics
        $allRfqs = RFQs::orderByRaw($priorityOrder)->limit($rfqLimit)->get();

        $stats = [
            'total' => $allRfqs->count(),
            'pending' => $allRfqs->where('status', 'pending')->count(),
            'accepted' => $allRfqs->where('status', 'accepted')->count(),
            'rejected' => $allRfqs->where('status', 'rejected')->count(),
            'closed' => $allRfqs->where('status', 'closed')->count(),
            'with_product' => $allRfqs->whereNotNull('product_id')->count(),
            'avg_response_time' => $allRfqs->where('status', '!=', 'pending')->avg(function($rfq) {
                return $rfq->messages->first() ?
                    $rfq->created_at->diffInHours($rfq->messages->first()->created_at) : 0;
            }),
        ];

        // Calculate percentages
        $stats['pending_percentage'] = $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100) : 0;
        $stats['accepted_percentage'] = $stats['total'] > 0 ? round(($stats['accepted'] / $stats['total']) * 100) : 0;
        $stats['response_rate'] = $stats['total'] > 0 ? round((($stats['accepted'] + $stats['rejected']) / $stats['total']) * 100) : 0;

        return view('vendor.rfq.index', compact('rfqs', 'vendor', 'rfqLimit', 'stats'));
    } catch (\Exception $e) {
        Log::error('Vendor RFQ Index Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('vendor.dashboard.home')->with('error', 'An error occurred while loading RFQs.');
    }
}

    /**
     * Show chat interface for a specific RFQ.
     */
    public function show(RFQs $rfq)
    {
        try {
            $vendor = $this->getVendor();

            // Check if vendor has a plan
            if (!$vendor->plan_id || !$vendor->plan) {
                return redirect()->route('vendor.dashboard.home')
                    ->with('error', 'You need to have a plan assigned to view RFQs. Please contact support.');
            }

            // Get the RFQ limit from plan (default to 5 if not set)
            $rfqLimit = $vendor->plan->buyer_rfqs_limit ?? 5;

            // Load RFQ with relationships
            $rfq->load(['product', 'category', 'businessProfile', 'country', 'messages.user']);

            // Get all messages as collection
            $allMessages = $rfq->messages()->with('user')->get();

            // Filter messages: show messages from vendor AND messages from buyer (conversation thread)
            $vendorId = $vendor->user_id;
            $messages = $allMessages->filter(function ($message) use ($vendorId) {
                // Include messages from the vendor
                if ($message->user_id == $vendorId && $message->sender_type === 'vendor') {
                    return true;
                }
                // Include messages from the buyer (in this conversation thread)
                if ($message->sender_type === 'buyer') {
                    return true;
                }
                // Include admin messages for context
                if ($message->sender_type === 'admin') {
                    return true;
                }
                return false;
            })->sortBy('created_at')->values();

            // Ensure messages is a collection
            if (!$messages instanceof \Illuminate\Support\Collection) {
                $messages = collect($messages);
            }

            $initialMessage = $rfq->message;

            return view('vendor.rfq.messages', compact('rfq', 'messages', 'initialMessage', 'vendor', 'allMessages'));
        } catch (\Exception $e) {
            Log::error('Vendor RFQ Show Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'rfq_id' => $rfq->id ?? null,
            ]);

            return redirect()->route('vendor.rfq.index')->with('error', 'An error occurred while loading the RFQ chat.');
        }
    }

    /**
     * Store a reply message for an RFQ.
     */
    public function storeMessage(Request $request, RFQs $rfq)
    {
        try {
            $vendor = $this->getVendor();

            // Check if RFQ is closed
            if ($rfq->isClosed()) {
                return response()->json(
                    [
                        'success' => false,
                        'error' => 'This RFQ is closed. No further messages can be sent.',
                    ],
                    403,
                );
            }

            $validated = $request->validate([
                'message' => 'required|string|min:1|max:5000',
            ]);

            // Create message
            $message = RFQMessage::create([
                'rfq_id' => $rfq->id,
                'user_id' => auth()->id(),
                'message' => $validated['message'],
                'sender_type' => 'vendor',
            ]);

            Log::info('RFQ message sent', [
                'message_id' => $message->id,
                'rfq_id' => $rfq->id,
                'vendor_id' => $vendor->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => $message->load('user'),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send RFQ message', [
                'error' => $e->getMessage(),
                'rfq_id' => $rfq->id ?? null,
            ]);

            return response()->json(
                [
                    'success' => false,
                    'error' => 'Failed to send message. Please try again.',
                ],
                500,
            );
        }
    }
}
