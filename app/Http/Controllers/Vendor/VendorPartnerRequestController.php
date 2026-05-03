<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PartnerRequest;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class VendorPartnerRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $partnerRequest = PartnerRequest::where('vendor_user_id', $user->id)
            ->latest()
            ->first();

        $partner = null;
        if ($partnerRequest) {
            $partner = Partner::where('partner_request_id', $partnerRequest->id)->first();
        }

        return view('vendor.partner-request.index', compact('partnerRequest', 'partner'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $existing = PartnerRequest::where('vendor_user_id', $user->id)->first();
        if ($existing) {
            return back()->with('error', 'You have already submitted a partner request.');
        }

        $request->validate([
            'company_name'  => 'required|string|max:255',
            'contact_name'  => 'required|string|max:255',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email|unique:partner_requests,email',
            'password'      => 'required|string|min:8|confirmed',
            'partner_type'  => 'nullable|string|max:100',
            'industry'      => 'nullable|string|max:100',
            'website_url'   => 'nullable|url|max:255',
            'country'       => 'nullable|string|max:100',
            'message'       => 'required|string|min:20',
            'phone'         => 'nullable|string|max:30',
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('partner-requests/logos', 'public');
        }

        // 1. Create the User with partner credentials (different email)
        $partnerUser = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'is_partner' => false, // will be set true when approved
        ]);

        // 2. Create PartnerRequest linked to both the partner user and the vendor user
        PartnerRequest::create([
            'user_id'        => $partnerUser->id,   // partner login user
            'vendor_user_id' => $user->id,           // vendor who submitted
            'company_name'   => $request->company_name,
            'contact_name'   => $request->contact_name,
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'partner_type'   => $request->partner_type,
            'industry'       => $request->industry,
            'website_url'    => $request->website_url,
            'country'        => $request->country,
            'message'        => $request->message,
            'phone'          => $request->phone,
            'logo'           => $logoPath,
            'status'         => 'pending',
        ]);

        return back()->with('success', 'Partner request submitted! We will review it within 2-3 business days.');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $partnerRequest = PartnerRequest::where('vendor_user_id', $user->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->firstOrFail();

        $request->validate([
            'company_name'  => 'required|string|max:255',
            'contact_name'  => 'required|string|max:255',
            'name'          => 'required|string|max:255',
            'partner_type'  => 'nullable|string|max:100',
            'industry'      => 'nullable|string|max:100',
            'website_url'   => 'nullable|url|max:255',
            'country'       => 'nullable|string|max:100',
            'message'       => 'required|string|min:20',
            'phone'         => 'nullable|string|max:30',
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
        ]);

        $data = $request->only([
            'company_name', 'contact_name', 'name',
            'partner_type', 'industry', 'website_url',
            'country', 'message', 'phone',
        ]);

        if ($request->hasFile('logo')) {
            if ($partnerRequest->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($partnerRequest->logo);
            }
            $data['logo'] = $request->file('logo')->store('partner-requests/logos', 'public');
        }

        // If rejected, reset to pending on re-submit
        if ($partnerRequest->status === 'rejected') {
            $data['status'] = 'pending';
            $data['rejection_reason'] = null;
        }

        $partnerRequest->update($data);

        // Also update the linked partner user's name
        if ($partnerRequest->user_id) {
            User::where('id', $partnerRequest->user_id)->update(['name' => $request->name]);
        }

        return back()->with('success', 'Partner request updated successfully.');
    }

    public function switchToPartner()
    {
        $user = auth()->user();

        $partnerRequest = PartnerRequest::where('vendor_user_id', $user->id)->first();

        if (!$partnerRequest) {
            return response()->json(['success' => false, 'message' => 'No partner request found.'], 404);
        }

        $partner = Partner::where('partner_request_id', $partnerRequest->id)->first();

        if (!$partner) {
            return response()->json(['success' => false, 'message' => 'Partner account has not been activated yet.'], 404);
        }

        $partnerUser = User::find($partnerRequest->user_id);

        if (!$partnerUser) {
            return response()->json(['success' => false, 'message' => 'Partner user account not found.'], 404);
        }

        $token = Str::random(60);
        Cache::put('partner_login_token_' . $token, $partnerUser->id, now()->addMinutes(5));

        return response()->json([
            'success'   => true,
            'message'   => 'Redirecting to your partner dashboard…',
            'login_url' => route('auth.partner.token-login', ['token' => $token]),
        ]);
    }
}
