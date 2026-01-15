<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PromoCodeController extends Controller
{
    /**
     * Get the authenticated vendor.
     */
    private function getVendor()
    {
        $vendor = Vendor::with('businessProfile')
            ->where('user_id', auth()->id())
            ->first();

        if (!$vendor) {
            abort(403, 'Vendor profile not found.');
        }

        return $vendor;
    }

    /**
     * Display a listing of promo codes.
     */
    public function index(Request $request)
    {
        try {
            $vendor = $this->getVendor();

            $query = PromoCode::where('created_by', auth()->id())
                ->withCount('products');

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Discount type filter
            if ($request->filled('discount_type')) {
                $query->where('discount_type', $request->discount_type);
            }

            // Validity filter
            if ($request->filled('validity')) {
                $now = now();
                switch ($request->validity) {
                    case 'upcoming':
                        $query->where('start_date', '>', $now);
                        break;
                    case 'current':
                        $query->where('start_date', '<=', $now)
                            ->where('end_date', '>=', $now);
                        break;
                    case 'expired':
                        $query->where('end_date', '<', $now);
                        break;
                }
            }

            // Usage filter
            if ($request->filled('usage')) {
                switch ($request->usage) {
                    case 'unused':
                        $query->where('usage_count', 0);
                        break;
                    case 'used':
                        $query->where('usage_count', '>', 0);
                        break;
                    case 'exhausted':
                        $query->whereNotNull('usage_limit')
                            ->whereColumn('usage_count', '>=', 'usage_limit');
                        break;
                }
            }

            // Date range filter
            if ($request->filled('date_range')) {
                $dateRange = $request->date_range;
                if (strpos($dateRange, ' to ') !== false) {
                    $dates = explode(' to ', $dateRange);
                    if (count($dates) === 2) {
                        try {
                            $start = \Carbon\Carbon::parse(trim($dates[0]))->startOfDay();
                            $end = \Carbon\Carbon::parse(trim($dates[1]))->endOfDay();
                            $query->whereBetween('start_date', [$start, $end]);
                        } catch (\Exception $e) {
                            Log::warning('Invalid date range format: ' . $dateRange);
                        }
                    }
                }
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $promoCodes = $query->paginate(15)->withQueryString();

            // Calculate stats
            $allPromoCodes = PromoCode::where('created_by', auth()->id());
            $stats = [
                'active' => (clone $allPromoCodes)->where('status', 'active')->count(),
                'inactive' => (clone $allPromoCodes)->where('status', 'inactive')->count(),
                'expired' => (clone $allPromoCodes)->where('end_date', '<', now())->count(),
                'total_uses' => (clone $allPromoCodes)->sum('usage_count'),
            ];

            return view('vendor.promo-code.index', compact('promoCodes', 'vendor', 'stats'));
        } catch (\Exception $e) {
            Log::error('Promo Code Index Error: ' . $e->getMessage());
            return redirect()->route('vendor.dashboard.home')
                ->with('error', 'An error occurred while loading promo codes.');
        }
    }

    /**
     * Show the form for creating a new promo code.
     */
    public function create()
    {
        $vendor = $this->getVendor();
        $products = Product::where('user_id', auth()->id())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        $categories = ProductCategory::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('vendor.promo-code.create', compact('vendor', 'products', 'categories'));
    }

    /**
     * Store a newly created promo code.
     */
    public function store(Request $request)
    {
        $vendor = $this->getVendor();

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code',
            'description' => 'nullable|string|max:500',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'user_usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'applicable_to' => 'required|in:all,specific_products,specific_categories',
            'status' => 'required|in:active,inactive',
            'currency' => 'required|string|max:10',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        try {
            DB::beginTransaction();

            $validated['code'] = strtoupper($validated['code']);
            $validated['created_by'] = auth()->id();
            $validated['usage_count'] = 0;

            $promoCode = PromoCode::create($validated);

            if ($validated['applicable_to'] === 'specific_products' && !empty($validated['products'])) {
                $promoCode->products()->sync($validated['products']);
            }

            DB::commit();

            Log::info('Promo code created successfully', [
                'promo_code_id' => $promoCode->id,
                'code' => $promoCode->code,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('vendor.promo-code.index')
                ->with('success', 'Promo code created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create promo code', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to create promo code. Please try again.']);
        }
    }

    /**
     * Show the form for editing the specified promo code.
     */
    public function edit(PromoCode $promoCode)
    {
        $vendor = $this->getVendor();

        if ($promoCode->created_by !== auth()->id()) {
            abort(403, 'You can only edit your own promo codes.');
        }

        $promoCode->load('products');
        $products = Product::where('user_id', auth()->id())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        $categories = ProductCategory::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('vendor.promo-code.edit', compact('promoCode', 'vendor', 'products', 'categories'));
    }

    /**
     * Update the specified promo code.
     */
    public function update(Request $request, PromoCode $promoCode)
    {
        $vendor = $this->getVendor();

        if ($promoCode->created_by !== auth()->id()) {
            abort(403, 'You can only update your own promo codes.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code,' . $promoCode->id,
            'description' => 'nullable|string|max:500',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'user_usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'applicable_to' => 'required|in:all,specific_products,specific_categories',
            'status' => 'required|in:active,inactive',
            'currency' => 'required|string|max:10',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        try {
            DB::beginTransaction();

            $validated['code'] = strtoupper($validated['code']);

            $promoCode->update($validated);

            if ($validated['applicable_to'] === 'specific_products') {
                $promoCode->products()->sync($validated['products'] ?? []);
            } else {
                $promoCode->products()->detach();
            }

            DB::commit();

            Log::info('Promo code updated successfully', ['promo_code_id' => $promoCode->id]);
            return redirect()->route('vendor.promo-code.index')
                ->with('success', 'Promo code updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update promo code', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to update promo code. Please try again.']);
        }
    }

    /**
     * Remove the specified promo code.
     */
    public function destroy(PromoCode $promoCode)
    {
        $vendor = $this->getVendor();

        if ($promoCode->created_by !== auth()->id()) {
            abort(403, 'You can only delete your own promo codes.');
        }

        try {
            $promoCode->delete();

            Log::info('Promo code deleted successfully', ['promo_code_id' => $promoCode->id]);
            return redirect()->route('vendor.promo-code.index')
                ->with('success', 'Promo code deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete promo code', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to delete promo code. Please try again.']);
        }
    }

    /**
     * Toggle promo code status.
     */
    public function toggleStatus(PromoCode $promoCode)
    {
        $vendor = $this->getVendor();

        if ($promoCode->created_by !== auth()->id()) {
            abort(403, 'You can only toggle status of your own promo codes.');
        }

        try {
            $promoCode->status = $promoCode->status === 'active' ? 'inactive' : 'active';
            $promoCode->save();

            Log::info('Promo code status toggled', [
                'promo_code_id' => $promoCode->id,
                'new_status' => $promoCode->status
            ]);

            return back()->with('success', 'Promo code status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to toggle promo code status', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to update status. Please try again.']);
        }
    }
}
