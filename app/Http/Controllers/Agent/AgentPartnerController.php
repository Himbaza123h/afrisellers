<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AgentPartnerController extends Controller
{
    public function index()
    {
        $partners = PartnerRequest::where('registered_by_agent_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('agent.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('agent.partners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email|unique:partner_requests,email',
            'password'     => 'required|string|min:8|confirmed',
            'partner_type' => 'nullable|string|max:100',
            'industry'     => 'nullable|string|max:100',
            'website_url'  => 'nullable|url|max:255',
            'country'      => 'nullable|string|max:100',
            'message'      => 'required|string|min:20',
            'phone'        => 'nullable|string|max:30',
            'logo'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('partner-requests/logos', 'public');
        }

        // 1. Create User with partner credentials
        $partnerUser = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'is_partner' => false,
        ]);

        // 2. Create PartnerRequest
        PartnerRequest::create([
            'user_id'                 => $partnerUser->id,
            'registered_by_agent_id'  => auth()->id(),
            'company_name'            => $request->company_name,
            'contact_name'            => $request->contact_name,
            'name'                    => $request->name,
            'email'                   => $request->email,
            'password'                => Hash::make($request->password),
            'partner_type'            => $request->partner_type,
            'industry'                => $request->industry,
            'website_url'             => $request->website_url,
            'country'                 => $request->country,
            'message'                 => $request->message,
            'phone'                   => $request->phone,
            'logo'                    => $logoPath,
            'status'                  => 'pending',
        ]);

        return redirect()->route('agent.partners.index')
            ->with('success', 'Partner registered successfully.');
    }

    public function show($id)
    {
        $partnerRequest = PartnerRequest::where('registered_by_agent_id', auth()->id())
            ->findOrFail($id);

        $partner = Partner::where('partner_request_id', $partnerRequest->id)->first();

        return view('agent.partners.show', compact('partnerRequest', 'partner'));
    }

    public function switchToPartner($id)
    {
        $partnerRequest = PartnerRequest::where('registered_by_agent_id', auth()->id())
            ->findOrFail($id);

        $partner = Partner::where('partner_request_id', $partnerRequest->id)->first();

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'This partner has not been approved yet.',
            ], 404);
        }

        $partnerUser = User::find($partnerRequest->user_id);

        if (!$partnerUser) {
            return response()->json([
                'success' => false,
                'message' => 'Partner user account not found.',
            ], 404);
        }

        $token = Str::random(60);
        Cache::put('partner_login_token_' . $token, $partnerUser->id, now()->addMinutes(5));

        return response()->json([
            'success'   => true,
            'message'   => 'Redirecting to partner dashboard…',
            'login_url' => route('auth.partner.token-login', ['token' => $token]),
        ]);
    }
}
