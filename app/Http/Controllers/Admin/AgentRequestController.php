<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentRequest;
use App\Models\Agent;
use App\Models\Country;
use App\Models\Role;
use App\Models\Notification;
use App\Mail\AgentRequestApprovedMail;
use App\Mail\AgentRequestRejectedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AgentRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = AgentRequest::with(['user', 'country', 'respondedBy'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            )->orWhere('city', 'like', "%{$search}%")
             ->orWhere('company_name', 'like', "%{$search}%");
        }

        $agentRequests = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => AgentRequest::count(),
            'pending'  => AgentRequest::where('status', 'pending')->count(),
            'approved' => AgentRequest::where('status', 'approved')->count(),
            'rejected' => AgentRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.agent-requests.index', compact('agentRequests', 'stats'));
    }

    public function show(AgentRequest $agentRequest)
    {
        $agentRequest->load(['user', 'country', 'respondedBy']);
        return view('admin.agent-requests.show', compact('agentRequest'));
    }

    public function approve(AgentRequest $agentRequest)
    {
        if (!$agentRequest->isPending()) {
            return back()->with('error', 'This request has already been responded to.');
        }

        try {
            DB::beginTransaction();

            $user = $agentRequest->user;

            // Assign agent role
            $agentRole = Role::where('slug', 'agent')->firstOrCreate(
                ['slug' => 'agent'],
                ['name' => 'Agent', 'description' => 'Sales Agent']
            );
            if (!$user->roles()->where('role_id', $agentRole->id)->exists()) {
                $user->assignRole($agentRole);
            }

            // Create Agent record if not already one
            if (!Agent::where('user_id', $user->id)->exists()) {
                Agent::create([
                    'user_id'           => $user->id,
                    'country_id'        => $agentRequest->country_id,
                    'phone'             => $agentRequest->phone,
                    'phone_code'        => $agentRequest->phone_code,
                    'city'              => $agentRequest->city,
                    'company_name'      => $agentRequest->company_name,
                    'commission_rate'   => $agentRequest->commission_rate ?? 5,
                    'account_status'    => 'active',
                    'email_verified'    => true,
                    'email_verified_at' => now(),
                ]);
            }

            // Mark request approved
            $agentRequest->update([
                'status'       => 'approved',
                'responded_by' => auth()->id(),
                'responded_at' => now(),
            ]);

            // In-app notification
            Notification::create([
                'title'   => 'Agent Request Approved 🎉',
                'content' => 'Congratulations! Your agent request has been approved. Your account has been upgraded.',
                'link_url'=> '/agent/dashboard',
                'user_id' => $user->id,
                'is_read' => false,
            ]);

            // Email
            try {
                Mail::to($user->email)
                    ->send(new AgentRequestApprovedMail($user->name, $user->email));
            } catch (\Exception $e) {
                Log::error('Agent approval email failed: ' . $e->getMessage());
            }

            DB::commit();

            return back()->with('success', "{$user->name}'s agent request has been approved and their account upgraded.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent Request Approve Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve request: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, AgentRequest $agentRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ], [
            'rejection_reason.required' => 'Please provide a reason for rejecting this request.',
            'rejection_reason.min'      => 'Reason must be at least 10 characters.',
        ]);

        if (!$agentRequest->isPending()) {
            return back()->with('error', 'This request has already been responded to.');
        }

        try {
            $agentRequest->update([
                'status'           => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'responded_by'     => auth()->id(),
                'responded_at'     => now(),
            ]);

            $user = $agentRequest->user;

            // In-app notification
            Notification::create([
                'title'   => 'Agent Request Update',
                'content' => 'Your agent request could not be approved at this time. Please check your email for details.',
                'user_id' => $user->id,
                'is_read' => false,
            ]);

            // Email
            try {
                Mail::to($user->email)
                    ->send(new AgentRequestRejectedMail($user->name, $request->rejection_reason));
            } catch (\Exception $e) {
                Log::error('Agent rejection email failed: ' . $e->getMessage());
            }

            return back()->with('success', "Request rejected and {$user->name} notified by email.");

        } catch (\Exception $e) {
            Log::error('Agent Request Reject Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject request.');
        }
    }

    public function destroy(AgentRequest $agentRequest)
{
    try {
        $agentRequest->delete(); // soft delete

        return back()->with('success', 'Agent request has been deleted.');

    } catch (\Exception $e) {
        Log::error('Agent Request Delete Error: ' . $e->getMessage());
        return back()->with('error', 'Failed to delete request.');
    }
}
}
