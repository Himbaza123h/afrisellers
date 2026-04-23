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
            $query->where(function($q) use ($s) {
                $q->where('company_name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('contact_name', 'like', "%$s%");
            });
        }

        $requests = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => PartnerRequest::count(),
            'pending'  => PartnerRequest::pending()->count(),
            'approved' => PartnerRequest::approved()->count(),
            'rejected' => PartnerRequest::rejected()->count(),
        ];

        return view('admin.partner-requests.index', compact('requests', 'stats'));
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

        // Auto-create as Partner
        Partner::create([
            'name'         => $partnerRequest->company_name,
            'logo'         => $partnerRequest->logo,
            'website_url'  => $partnerRequest->website_url,
            'industry'     => $partnerRequest->industry,
            'partner_type' => $partnerRequest->partner_type,
            'description'  => $partnerRequest->message,
            'is_active'    => true,
            'sort_order'   => Partner::max('sort_order') + 1,
        ]);

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
