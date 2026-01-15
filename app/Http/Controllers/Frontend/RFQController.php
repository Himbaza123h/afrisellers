<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\RFQs;
use App\Models\Country;
use App\Models\Product;
use App\Models\BusinessProfile;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RFQController extends Controller
{
    /**
     * Show the form for creating a new RFQ.
     */
    public function create()
    {
        $countries = Country::where('status', 'active')->orderBy('name')->get();
        $products = Product::where('status', 'active')
            ->where('is_admin_verified', true)
            ->orderBy('name')
            ->get();

        $businessProfiles = BusinessProfile::where('verification_status', 'verified')
            ->where('is_admin_verified', true)
            ->with('country')
            ->orderBy('business_name')
            ->get();

        $categories = ProductCategory::where('status', 'active')
            ->orderBy('name')
            ->get();

        // Pre-fill user data if authenticated
        $user = Auth::user();
        $userData = null;
        if ($user) {
            $userData = [
                'name' => $user->name,
                'email' => $user->email,
            ];
        }

        return view('frontend.rfq.create', compact('countries', 'products', 'businessProfiles', 'categories', 'userData'));
    }

    /**
     * Store a newly created RFQ in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'business_id' => 'nullable|exists:business_profiles,id',
            'category_id' => 'nullable|exists:product_categories,id',
            'message' => 'required|string|min:10|max:2000',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'phone_code' => 'required|string|max:10',
            'country_id' => 'required|exists:countries,id',
            'city' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
        ], [
            'message.required' => 'Please provide a message for your RFQ.',
            'message.min' => 'Message must be at least 10 characters.',
            'message.max' => 'Message cannot exceed 2000 characters.',
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'phone.required' => 'Phone number is required.',
            'phone_code.required' => 'Phone code is required.',
            'country_id.required' => 'Country is required.',
            'country_id.exists' => 'Selected country is invalid.',
            'city.required' => 'City is required.',
            'product_id.exists' => 'Selected product is invalid.',
            'business_id.exists' => 'Selected business is invalid.',
            'category_id.exists' => 'Selected category is invalid.',
        ]);

        // Custom validation: At least one of product_id, business_id, or category_id must be provided
        if (empty($validated['product_id']) && empty($validated['business_id']) && empty($validated['category_id'])) {
            return back()
                ->withInput()
                ->withErrors(['product_id' => 'Please select at least one: Product, Business, or Category.']);
        }

        try {
            // Set user_id if authenticated, otherwise null
            $validated['user_id'] = Auth::check() ? Auth::id() : null;
            $validated['status'] = 'pending';

            $rfq = RFQs::create($validated);

            Log::info('RFQ created successfully', [
                'rfq_id' => $rfq->id,
                'email' => $rfq->email,
                'user_id' => $rfq->user_id,
            ]);

            return redirect()
                ->route('rfqs.create')
                ->with('success', 'Your RFQ has been submitted successfully! We will contact you soon.');
        } catch (\Exception $e) {
            Log::error('Failed to create RFQ', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to submit RFQ. Please try again.']);
        }
    }

    /**
     * Upload image for rich text editor
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB max
        ]);

        try {
            $path = $request->file('image')->store('rfq/images', 'public');
            $url = asset('storage/' . $path);

            return response()->json([
                'success' => true,
                'url' => $url,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to upload image', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image. Please try again.',
            ], 500);
        }
    }

    /**
     * Upload file/attachment for rich text editor
     */
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,txt|max:10240', // 10MB max
        ]);

        try {
            $path = $request->file('file')->store('rfq/attachments', 'public');
            $url = asset('storage/' . $path);

            return response()->json([
                'success' => true,
                'url' => $url,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to upload file', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file. Please try again.',
            ], 500);
        }
    }
}
