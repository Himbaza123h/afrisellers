<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Role;
use App\Models\BusinessProfile;
use App\Models\User;
use App\Models\Vendor\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\AgentCredit;
use App\Models\Credit;
use App\Models\CreditTransaction;
use Illuminate\Support\Facades\Cache;


class VendorController extends Controller
{
    // ─── Subscription guard ───────────────────────────────────────────
    // private function vendorLimit(): int
    // {
    //     $sub = \App\Models\AgentSubscription::where('agent_id', auth()->id())
    //         ->active()
    //         ->with('package')
    //         ->first();

    //     return (int) ($sub?->package?->max_vendors ?? 1);
    // }

    // private function currentVendorCount(): int
    // {
    //     return Vendor::where('agent_id', auth()->id())->count();
    // }

    // private function canAddMore(): bool
    // {
    //     return $this->currentVendorCount() < $this->vendorLimit();
    // }

    private function currentVendorCount(): int
{
    return Vendor::where('agent_id', auth()->id())->count();
}

    // ─── INDEX ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $agentId = auth()->id();

        $vendors = Vendor::where('agent_id', $agentId)
            ->with(['user', 'businessProfile.country'])
            ->when($request->search, fn($q) =>
                $q->whereHas('businessProfile', fn($q2) =>
                    $q2->where('business_name', 'like', "%{$request->search}%")
                )->orWhereHas('user', fn($q2) =>
                    $q2->where('email', 'like', "%{$request->search}%")
                        ->orWhere('name', 'like', "%{$request->search}%")
                )
            )
            ->when($request->status, fn($q) =>
                $q->where('account_status', $request->status)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'     => Vendor::where('agent_id', $agentId)->count(),
            'active'    => Vendor::where('agent_id', $agentId)->where('account_status', 'active')->count(),
            'pending'   => Vendor::where('agent_id', $agentId)->where('account_status', 'pending')->count(),
            'suspended' => Vendor::where('agent_id', $agentId)->where('account_status', 'suspended')->count(),
            'limit'     => 0,
        ];

        return view('agent.vendors.index', compact('vendors', 'stats'));
    }


    public function switchToVendor($vendorId)
{
    $vendor = Vendor::where('agent_id', auth()->id())
        ->with('user')
        ->findOrFail($vendorId);

    if (!$vendor->user) {
        return response()->json(['success' => false, 'message' => 'Vendor user not found.'], 404);
    }

    if ($vendor->account_status !== 'active') {
        return response()->json(['success' => false, 'message' => 'Vendor account is not active.'], 403);
    }

    $token = Str::random(60);

    Cache::put(
        'vendor_login_token_' . $token,
        $vendor->user->id,
        now()->addMinutes(5)
    );

    return response()->json([
        'success'   => true,
        'message'   => 'Ready to switch',
        'login_url' => route('auth.vendor.token-login', ['token' => $token]),
    ]);
}

    // ─── PRINT ────────────────────────────────────────────────────────
    public function print()
    {
        $vendors = Vendor::where('agent_id', auth()->id())
            ->with(['user', 'businessProfile.country'])
            ->latest()
            ->get();

        return view('agent.vendors.print', compact('vendors'));
    }

    // ─── CREATE ───────────────────────────────────────────────────────
    public function create()
    {
        $countries = Country::orderBy('name')->get();
        return view('agent.vendors.create', compact('countries'));
    }

    // ─── STORE ────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // if (!$this->canAddMore()) {
        //     return redirect()->route('agent.vendors.index')
        //         ->with('error', 'Vendor limit reached. Please upgrade your subscription.');
        // }

        $validated = $request->validate([
            // Contact / Login
            'name'                        => 'required|string|max:255',
            'email'                       => 'required|email|unique:users,email',
            'password'                    => 'required|string|min:8|confirmed',
            'phone'                       => 'required|string|max:30',
            'contact_person_position'     => 'nullable|string|max:100',
            'whatsapp_number'             => 'nullable|string|max:30',

            // Business core
            'country_id'                  => 'required|exists:countries,id',
            'business_name'               => 'required|string|max:255',
            'business_type'               => 'nullable|string|max:100',
            'business_registration_number'=> 'nullable|string|max:100',
            'tax_id'                      => 'nullable|string|max:100',
            'year_established'            => 'nullable|integer|min:1900|max:' . date('Y'),
            'company_size'                => 'nullable|string|max:50',
            'annual_revenue'              => 'nullable|string|max:50',

            // Location
            'city'                        => 'nullable|string|max:100',
            'postal_code'                 => 'nullable|string|max:20',
            'address'                     => 'nullable|string|max:500',

            // Online presence
            'website'                     => 'nullable|url|max:255',
            'business_email'              => 'nullable|email|max:255',
            'operating_hours'             => 'nullable|string|max:255',
            'languages_spoken'            => 'nullable|string|max:255',
            'description'                 => 'nullable|string',

            // Operations
            'main_products'               => 'nullable|string|max:500',
            'export_markets'              => 'nullable|string|max:500',
            'production_capacity'         => 'nullable|string|max:255',
            'minimum_order_value'         => 'nullable|numeric|min:0',
            'payment_terms'               => 'nullable|string|max:255',
            'delivery_time'               => 'nullable|string|max:255',
            'quality_control'             => 'nullable|string',
            'certifications'              => 'nullable|string|max:500',

            // Social media
            'facebook_link'               => 'nullable|url|max:255',
            'twitter_link'                => 'nullable|url|max:255',
            'linkedin_link'               => 'nullable|url|max:255',
            'instagram_link'              => 'nullable|url|max:255',
            'youtube_link'                => 'nullable|url|max:255',
        ]);

        DB::beginTransaction();
        try {

            $user = User::create([
                'name'              => $validated['name'],
                'email'             => $validated['email'],
                'password'          => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ]);

            // 2. Assign vendor role
            $vendorRole = Role::where('slug', 'vendor')->first();
            if ($vendorRole) {
                $user->assignRole($vendorRole);
            }

            // 3. Create business profile (pre-verified since agent is vouching)
            $businessProfile = BusinessProfile::create([
                'user_id'                      => $user->id,
                'country_id'                   => $validated['country_id'],
                'business_name'                => $validated['business_name'],
                'phone'                        => $validated['phone'],
                'business_email'               => $validated['business_email'] ?? $validated['email'],
                'business_type'                => $validated['business_type'] ?? null,
                'business_registration_number' => $validated['business_registration_number'] ?? null,
                'tax_id'                       => $validated['tax_id'] ?? null,
                'year_established'             => $validated['year_established'] ?? null,
                'company_size'                 => $validated['company_size'] ?? null,
                'annual_revenue'               => $validated['annual_revenue'] ?? null,
                'city'                         => $validated['city'] ?? null,
                'postal_code'                  => $validated['postal_code'] ?? null,
                'address'                      => $validated['address'] ?? null,
                'website'                      => $validated['website'] ?? null,
                'description'                  => $validated['description'] ?? null,
                'operating_hours'              => $validated['operating_hours'] ?? null,
                'languages_spoken'             => $validated['languages_spoken'] ?? null,
                'whatsapp_number'              => $validated['whatsapp_number'] ?? null,
                'main_products'                => $validated['main_products'] ?? null,
                'export_markets'               => $validated['export_markets'] ?? null,
                'production_capacity'          => $validated['production_capacity'] ?? null,
                'minimum_order_value'          => $validated['minimum_order_value'] ?? null,
                'payment_terms'                => $validated['payment_terms'] ?? null,
                'delivery_time'                => $validated['delivery_time'] ?? null,
                'quality_control'              => $validated['quality_control'] ?? null,
                'certifications'               => $validated['certifications'] ?? null,
                'facebook_link'                => $validated['facebook_link'] ?? null,
                'twitter_link'                 => $validated['twitter_link'] ?? null,
                'linkedin_link'                => $validated['linkedin_link'] ?? null,
                'instagram_link'               => $validated['instagram_link'] ?? null,
                'youtube_link'                 => $validated['youtube_link'] ?? null,
                'contact_person_name'          => $validated['name'],
                'contact_person_position'      => $validated['contact_person_position'] ?? null,
                'verification_status'          => 'verified',
                'is_admin_verified'            => true,
            ]);

            // 4. Create vendor record linked to this agent
            $vendor = Vendor::create([
                'user_id'                  => $user->id,
                'agent_id'                 => auth()->id(),
                'business_profile_id'      => $businessProfile->id,
                'plan_id'                  => 1,
                'email_verification_token' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                'email_verified_at'        => now(),
                'account_status'           => 'active',
                'email_verified'           => true,

            ]);

            // 5. Link vendor back to business profile
            $businessProfile->update(['vendor_id' => $vendor->id]);

            Mail::to($user->email)->queue(
                new \App\Mail\VendorAccountCreatedMail($user->name, $user->email, $validated['password'])
            );

            Mail::to(auth()->user()->email)->queue(
                new \App\Mail\AgentVendorCreatedMail(auth()->user()->name, $user->name, $user->email)
            );

            DB::commit();

            // ── Award credits for vendor registration ──────────────────────
// ── Deduct credits for vendor registration ─────────────────────
            try {
                $creditEntry  = Credit::where('type', 'agent_registration')->first();
                $creditAmount = $creditEntry ? (float) $creditEntry->value : 5.0;

                $agentCredit = AgentCredit::firstOrNew(['agent_id' => auth()->id()]);
                $agentCredit->total_credits = max(0, (float) ($agentCredit->total_credits ?? 0) - $creditAmount);
                $agentCredit->save();

                CreditTransaction::create([
                    'agent_id'         => auth()->id(),
                    'transaction_type' => 'vendor_registration_deduction',
                    'credits'          => -$creditAmount,
                ]);
            } catch (\Throwable $e) {
                \Log::error('Credit deduction failed for agent ' . auth()->id() . ': ' . $e->getMessage());
            }

            return redirect()->route('agent.vendors.show', $vendor->id)
                ->with('success', "Vendor created successfully!")
                ->with('vendor_email', $validated['email'])
                ->with('vendor_password', $validated['password']);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create vendor: ' . $e->getMessage());
        }
    }

    // ─── SHOW ─────────────────────────────────────────────────────────
        public function show($id)
        {
            $vendor = Vendor::where('agent_id', auth()->id())
                ->with(['user', 'businessProfile.country'])
                ->findOrFail($id);

            $recentProducts = \App\Models\Product::where('user_id', $vendor->user_id)
                ->with(['images', 'productCategory'])
                ->latest()
                ->take(2)
                ->get();

            return view('agent.vendors.show', compact('vendor', 'recentProducts'));
        }

    // ─── EDIT ─────────────────────────────────────────────────────────
    public function edit($id)
    {
        $vendor = Vendor::where('agent_id', auth()->id())
            ->with(['user', 'businessProfile'])
            ->findOrFail($id);

        $countries = Country::orderBy('name')->get();

        return view('agent.vendors.edit', compact('vendor', 'countries'));
    }

    // ─── UPDATE ───────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $vendor = Vendor::where('agent_id', auth()->id())
            ->with(['user', 'businessProfile'])
            ->findOrFail($id);

        $validated = $request->validate([
            // Contact
            'name'                        => 'required|string|max:255',
            'phone'                       => 'required|string|max:30',
            'contact_person_position'     => 'nullable|string|max:100',
            'whatsapp_number'             => 'nullable|string|max:30',

            // Business core
            'country_id'                  => 'required|exists:countries,id',
            'business_name'               => 'required|string|max:255',
            'business_type'               => 'nullable|string|max:100',
            'business_registration_number'=> 'nullable|string|max:100',
            'tax_id'                      => 'nullable|string|max:100',
            'year_established'            => 'nullable|integer|min:1900|max:' . date('Y'),
            'company_size'                => 'nullable|string|max:50',
            'annual_revenue'              => 'nullable|string|max:50',

            // Location
            'city'                        => 'nullable|string|max:100',
            'postal_code'                 => 'nullable|string|max:20',
            'address'                     => 'nullable|string|max:500',

            // Online presence
            'website'                     => 'nullable|url|max:255',
            'business_email'              => 'nullable|email|max:255',
            'operating_hours'             => 'nullable|string|max:255',
            'languages_spoken'            => 'nullable|string|max:255',
            'description'                 => 'nullable|string',

            // Operations
            'main_products'               => 'nullable|string|max:500',
            'export_markets'              => 'nullable|string|max:500',
            'production_capacity'         => 'nullable|string|max:255',
            'minimum_order_value'         => 'nullable|numeric|min:0',
            'payment_terms'               => 'nullable|string|max:255',
            'delivery_time'               => 'nullable|string|max:255',
            'quality_control'             => 'nullable|string',
            'certifications'              => 'nullable|string|max:500',

            // Social media
            'facebook_link'               => 'nullable|url|max:255',
            'twitter_link'                => 'nullable|url|max:255',
            'linkedin_link'               => 'nullable|url|max:255',
            'instagram_link'              => 'nullable|url|max:255',
            'youtube_link'                => 'nullable|url|max:255',
        ]);

        DB::beginTransaction();
        try {
            $vendor->user->update(['name' => $validated['name']]);

            $vendor->businessProfile->update([
                'business_name'                => $validated['business_name'],
                'phone'                        => $validated['phone'],
                'country_id'                   => $validated['country_id'],
                'business_type'                => $validated['business_type'] ?? null,
                'business_registration_number' => $validated['business_registration_number'] ?? null,
                'tax_id'                       => $validated['tax_id'] ?? null,
                'year_established'             => $validated['year_established'] ?? null,
                'company_size'                 => $validated['company_size'] ?? null,
                'annual_revenue'               => $validated['annual_revenue'] ?? null,
                'city'                         => $validated['city'] ?? null,
                'postal_code'                  => $validated['postal_code'] ?? null,
                'address'                      => $validated['address'] ?? null,
                'website'                      => $validated['website'] ?? null,
                'business_email'               => $validated['business_email'] ?? null,
                'operating_hours'              => $validated['operating_hours'] ?? null,
                'languages_spoken'             => $validated['languages_spoken'] ?? null,
                'description'                  => $validated['description'] ?? null,
                'whatsapp_number'              => $validated['whatsapp_number'] ?? null,
                'main_products'                => $validated['main_products'] ?? null,
                'export_markets'               => $validated['export_markets'] ?? null,
                'production_capacity'          => $validated['production_capacity'] ?? null,
                'minimum_order_value'          => $validated['minimum_order_value'] ?? null,
                'payment_terms'                => $validated['payment_terms'] ?? null,
                'delivery_time'                => $validated['delivery_time'] ?? null,
                'quality_control'              => $validated['quality_control'] ?? null,
                'certifications'               => $validated['certifications'] ?? null,
                'facebook_link'                => $validated['facebook_link'] ?? null,
                'twitter_link'                 => $validated['twitter_link'] ?? null,
                'linkedin_link'                => $validated['linkedin_link'] ?? null,
                'instagram_link'               => $validated['instagram_link'] ?? null,
                'youtube_link'                 => $validated['youtube_link'] ?? null,
                'contact_person_name'          => $validated['name'],
                'contact_person_position'      => $validated['contact_person_position'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('agent.vendors.show', $id)
                ->with('success', 'Vendor updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update vendor: ' . $e->getMessage());
        }
    }

    // ─── DESTROY (unlink, not hard delete) ───────────────────────────
    public function destroy($id)
    {
        $vendor = Vendor::where('agent_id', auth()->id())->findOrFail($id);
        $vendor->update(['agent_id' => null]);

        return redirect()->route('agent.vendors.index')
            ->with('success', 'Vendor removed from your account.');
    }

    // ─── STATUS ACTIONS ───────────────────────────────────────────────
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:active,pending,suspended,rejected']);
        $vendor = Vendor::where('agent_id', auth()->id())->findOrFail($id);
        $vendor->update(['account_status' => $request->status]);

        return back()->with('success', 'Vendor status updated.');
    }

    public function suspend($id)
    {
        $vendor = Vendor::where('agent_id', auth()->id())->findOrFail($id);
        $vendor->suspend();

        return back()->with('success', 'Vendor has been suspended.');
    }

    public function activate($id)
    {
        $vendor = Vendor::where('agent_id', auth()->id())->findOrFail($id);
        $vendor->activate();

        return back()->with('success', 'Vendor has been activated.');
    }

    // ─── TRANSFER ─────────────────────────────────────────────────────
    public function transfer(Request $request, $id)
    {
        $request->validate(['new_agent_id' => 'required|exists:users,id']);
        $vendor = Vendor::where('agent_id', auth()->id())->findOrFail($id);
        $vendor->update(['agent_id' => $request->new_agent_id]);

        return redirect()->route('agent.vendors.index')
            ->with('success', 'Vendor transferred successfully.');
    }

    // ─── EXPORT CSV ───────────────────────────────────────────────────
    public function export()
    {
        $vendors = Vendor::where('agent_id', auth()->id())
            ->with(['user', 'businessProfile.country'])
            ->latest()
            ->get();

        $filename = 'my-vendors-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($vendors) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Business Name', 'Contact Person', 'Email', 'Phone', 'Country', 'City', 'Status', 'Joined']);
            foreach ($vendors as $v) {
                fputcsv($out, [
                    $v->businessProfile?->business_name ?? 'N/A',
                    $v->user?->name ?? 'N/A',
                    $v->user?->email ?? 'N/A',
                    $v->businessProfile?->phone ?? 'N/A',
                    $v->businessProfile?->country?->name ?? 'N/A',
                    $v->businessProfile?->city ?? 'N/A',
                    $v->account_status,
                    $v->created_at->format('Y-m-d'),
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    // ─── COMMISSIONS for a single vendor ─────────────────────────────
    public function commissions($id)
    {
        $vendor = Vendor::where('agent_id', auth()->id())
            ->with(['user', 'businessProfile'])
            ->findOrFail($id);

        return view('agent.vendors.commissions', compact('vendor'));
    }

    // ─── ORDERS for a single vendor ──────────────────────────────────
    public function orders($id)
    {
        $vendor = Vendor::where('agent_id', auth()->id())
            ->with(['user', 'businessProfile'])
            ->findOrFail($id);

        return view('agent.vendors.orders', compact('vendor'));
    }
}
