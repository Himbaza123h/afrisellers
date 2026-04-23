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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RFQController extends Controller
{
    /**
     * Show the form for creating a new RFQ.
     */
    public function create()
    {
        Log::info('[RFQ] Loading create form', [
            'user_id'    => Auth::id() ?? 'guest',
            'user_email' => Auth::user()?->email ?? 'guest',
            'ip'         => request()->ip(),
        ]);

        $countries = Country::where('status', 'active')->orderBy('name')->get();
        Log::info('[RFQ] Loaded countries', ['count' => $countries->count()]);

        $products = Product::where('status', 'active')
            ->where('is_admin_verified', true)
            ->orderBy('name')
            ->get();
        Log::info('[RFQ] Loaded products', ['count' => $products->count()]);

        $businessProfiles = BusinessProfile::where('verification_status', 'verified')
            ->where('is_admin_verified', true)
            ->with('country')
            ->orderBy('business_name')
            ->get();
        Log::info('[RFQ] Loaded business profiles', ['count' => $businessProfiles->count()]);

        $categories = ProductCategory::where('status', 'active')
            ->orderBy('name')
            ->get();
        Log::info('[RFQ] Loaded categories', ['count' => $categories->count()]);

        // Pre-fill user data if authenticated
        $user     = Auth::user();
        $userData = null;
        if ($user) {
            $userData = [
                'name'  => $user->name,
                'email' => $user->email,
            ];
            Log::info('[RFQ] Pre-filling user data', ['user_id' => $user->id]);
        }

        return view('frontend.rfq.create', compact(
            'countries',
            'products',
            'businessProfiles',
            'categories',
            'userData'
        ));
    }

    /**
     * Store a newly created RFQ in storage.
     */
    public function store(Request $request)
    {
        Log::info('[RFQ] Store attempt started', [
            'user_id'      => Auth::id() ?? 'guest',
            'ip'           => $request->ip(),
            'has_product'  => $request->filled('product_id'),
            'has_business' => $request->filled('business_id'),
            'has_category' => $request->filled('category_id'),
            'message_len'  => strlen($request->input('message', '')),
            'email'        => $request->input('email'),
            'country_id'   => $request->input('country_id'),
            'city'         => $request->input('city'),
        ]);

        $validated = $request->validate([
            'product_id'  => 'nullable|exists:products,id',
            'business_id' => 'nullable|exists:business_profiles,id',
            'category_id' => 'nullable|exists:product_categories,id',
            'message'     => 'required|string|min:10|max:5000',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'required|string|max:20',
            'phone_code'  => 'required|string|max:10',
            'country_id'  => 'required|exists:countries,id',
            'city'        => 'required|string|max:100',
            'address'     => 'nullable|string|max:255',
        ], [
            'message.required'    => 'Please provide a message for your RFQ.',
            'message.min'         => 'Message must be at least 10 characters.',
            'message.max'         => 'Message cannot exceed 5000 characters.',
            'name.required'       => 'Name is required.',
            'email.required'      => 'Email is required.',
            'email.email'         => 'Please provide a valid email address.',
            'phone.required'      => 'Phone number is required.',
            'phone_code.required' => 'Phone code is required.',
            'country_id.required' => 'Country is required.',
            'country_id.exists'   => 'Selected country is invalid.',
            'city.required'       => 'City is required.',
            'product_id.exists'   => 'Selected product is invalid.',
            'business_id.exists'  => 'Selected business is invalid.',
            'category_id.exists'  => 'Selected category is invalid.',
        ]);

        Log::info('[RFQ] Validation passed', [
            'product_id'  => $validated['product_id']  ?? null,
            'business_id' => $validated['business_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'email'       => $validated['email'],
        ]);

        // Custom validation: at least one of product/business/category must be set
        if (
            empty($validated['product_id']) &&
            empty($validated['business_id']) &&
            empty($validated['category_id'])
        ) {
            Log::warning('[RFQ] Validation failed — no product/business/category selected', [
                'email' => $validated['email'],
                'ip'    => $request->ip(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['product_id' => 'Please select at least one: Product, Business, or Category.']);
        }

        try {
            // ── KEY FIX ──────────────────────────────────────────────────────────
            // Merge phone_code + phone into a single "phone" string so we don't
            // try to insert a "phone_code" column that may not exist in the DB.
            $phoneCode   = $validated['phone_code'];
            $phoneNumber = $validated['phone'];

            // Build the data array with only the columns that exist in the table
            $data = [
                'product_id'  => $validated['product_id']  ?? null,
                'business_id' => $validated['business_id'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'message'     => $validated['message'],
                'name'        => $validated['name'],
                'email'       => $validated['email'],
                // Store as "+250 788000000" — single column, no schema change needed
                'phone'       => $phoneCode . ' ' . $phoneNumber,
                'country_id'  => $validated['country_id'],
                'city'        => $validated['city'],
                'address'     => $validated['address'] ?? null,
                'user_id'     => Auth::check() ? Auth::id() : null,
                'status'      => 'pending',
            ];

            Log::info('[RFQ] About to create RFQ record', $data);

            $rfq = RFQs::create($data);

            Log::info('[RFQ] Created successfully', [
                'rfq_id'  => $rfq->id,
                'email'   => $rfq->email,
                'user_id' => $rfq->user_id,
                'status'  => $rfq->status,
            ]);

            return redirect()
                ->route('rfqs.create')
                ->with('success', 'Your RFQ has been submitted successfully! We will contact you soon.');

        } catch (\Exception $e) {
            Log::error('[RFQ] Failed to create RFQ', [
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'email'   => $validated['email'] ?? null,
                'user_id' => Auth::id() ?? null,
                'ip'      => $request->ip(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to submit RFQ: ' . $e->getMessage()]);
        }
    }

    /**
     * Translate the RFQ message using Google Translate (free endpoint).
     *
     * POST /rfqs/translate
     */
    public function translate(Request $request)
    {
        $request->validate([
            'text'            => 'required|string|max:5000',
            'target_language' => 'required|string|in:french,swahili,english,arabic,portuguese,spanish',
        ]);

        $text           = $request->input('text');
        $targetLanguage = $request->input('target_language');

        $langMap = [
            'french'     => 'fr',
            'swahili'    => 'sw',
            'english'    => 'en',
            'arabic'     => 'ar',
            'portuguese' => 'pt',
            'spanish'    => 'es',
        ];

        $langCode = $langMap[$targetLanguage] ?? 'en';

        try {
            $response = Http::get('https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx',
                'sl'     => 'auto',
                'tl'     => $langCode,
                'dt'     => 't',
                'q'      => strip_tags($text),
            ]);

            if ($response->failed()) {
                return response()->json(['success' => false, 'message' => 'Translation failed.'], 500);
            }

            $result     = $response->json();
            $translated = collect($result[0])->pluck(0)->implode('');

            return response()->json([
                'success'    => true,
                'translated' => $translated,
                'language'   => $targetLanguage,
            ]);

        } catch (\Exception $e) {
            Log::error('[RFQ] Translation error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Translation error.'], 500);
        }
    }

    /**
     * Upload image for rich text editor
     */
    public function uploadImage(Request $request)
    {
        Log::info('[RFQ] Image upload attempt', [
            'user_id' => Auth::id() ?? 'guest',
            'ip'      => $request->ip(),
        ]);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif|max:5120',
        ]);

        try {
            $path = $request->file('image')->store('rfq/images', 'public');
            $url  = asset('storage/' . $path);

            Log::info('[RFQ] Image uploaded successfully', [
                'path'    => $path,
                'url'     => $url,
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return response()->json([
                'success' => true,
                'url'     => $url,
            ]);

        } catch (\Exception $e) {
            Log::error('[RFQ] Image upload failed', [
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'user_id' => Auth::id() ?? 'guest',
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
        Log::info('[RFQ] File upload attempt', [
            'user_id'   => Auth::id() ?? 'guest',
            'ip'        => $request->ip(),
            'mime_type' => $request->file('file')?->getMimeType(),
        ]);

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,txt|max:10240',
        ]);

        try {
            $path = $request->file('file')->store('rfq/attachments', 'public');
            $url  = asset('storage/' . $path);

            Log::info('[RFQ] File uploaded successfully', [
                'path'    => $path,
                'url'     => $url,
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return response()->json([
                'success' => true,
                'url'     => $url,
            ]);

        } catch (\Exception $e) {
            Log::error('[RFQ] File upload failed', [
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file. Please try again.',
            ], 500);
        }
    }
}
