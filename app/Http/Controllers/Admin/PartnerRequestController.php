<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerRequest;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PartnerRequest::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('company_name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('contact_name', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }

        if ($request->filled('partner_type')) {
            $query->where('partner_type', $request->partner_type);
        }

        if ($request->filled('industry')) {
            $query->where('industry', 'like', '%' . $request->industry . '%');
        }

        if ($request->filled('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => PartnerRequest::count(),
            'pending'  => PartnerRequest::pending()->count(),
            'approved' => PartnerRequest::approved()->count(),
            'rejected' => PartnerRequest::rejected()->count(),
        ];

        // For filter dropdowns
        $partnerTypes = PartnerRequest::select('partner_type')
            ->whereNotNull('partner_type')
            ->distinct()
            ->orderBy('partner_type')
            ->pluck('partner_type');

        $countries = PartnerRequest::select('country')
            ->whereNotNull('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return view('admin.partner-requests.index', compact('requests', 'stats', 'partnerTypes', 'countries'));
    }

    public function show(PartnerRequest $partnerRequest)
    {
        return view('admin.partner-requests.show', compact('partnerRequest'));
    }

    public function approve(Request $request, PartnerRequest $partnerRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $partnerRequest->update([
            'status'      => 'approved',
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        Partner::create([
            'name'                => $partnerRequest->company_name,
            'logo'                => $partnerRequest->logo,
            'website_url'         => $partnerRequest->website_url,
            'industry'            => $partnerRequest->industry,
            'partner_type'        => $partnerRequest->partner_type,
            'description'         => $partnerRequest->about_us ?? $partnerRequest->message,
            'country'             => $partnerRequest->country,
            'presence_countries'  => $partnerRequest->presence_countries,
            'established'         => $partnerRequest->established,
            'services'            => $partnerRequest->services,
            'intro'               => $partnerRequest->intro,
            'partner_request_id'  => $partnerRequest->id,
            'is_active'           => true,
            'sort_order'          => (Partner::max('sort_order') ?? 0) + 1,
        ]);

        // update is_partner on the user
        if ($partnerRequest->user) {

            $partnerRequest->user->update(['is_partner' => true]);
            $partnerRequest->update(['partner_user_id' =>$partnerRequest->user->id ]);
        }

        return back()->with('success', "'{$partnerRequest->company_name}' approved and added to Partners.");
    }

    public function reject(Request $request, PartnerRequest $partnerRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $partnerRequest->update([
            'status'      => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', "Request from '{$partnerRequest->company_name}' rejected.");
    }

    public function destroy(PartnerRequest $partnerRequest)
    {
        $partnerRequest->delete();
        return back()->with('success', 'Request deleted.');
    }
}
