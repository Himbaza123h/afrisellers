<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\Country;
use App\Models\User;
use App\Models\Notification;
use App\Models\Vendor\Vendor;
use App\Mail\AgentAccountCreatedMail;
use App\Mail\AgentVendorAssignedMail;
use App\Mail\AgentVendorRemovedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class AgentController extends Controller
{
    // ─── Index ───────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        try {
            $query = Agent::with(['user', 'country', 'businessProfile']);

            if ($request->filled('filter')) {
                $query->where('account_status', $request->filter);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"))
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                });
            }

            if ($request->filled('country')) {
                $query->where('country_id', $request->country);
            }

            if ($request->filled('email_verified')) {
                $query->where('email_verified', $request->email_verified);
            }

            if ($request->filled('commission_range')) {
                match ($request->commission_range) {
                    'high'   => $query->where('commission_earned', '>', 1000),
                    'medium' => $query->whereBetween('commission_earned', [500, 1000]),
                    'low'    => $query->where('commission_earned', '<', 500),
                    default  => null,
                };
            }

            if ($request->filled('date_range')) {
                match ($request->date_range) {
                    'today' => $query->whereDate('created_at', today()),
                    'week'  => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                    'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                    default => null,
                };
            }

            $sortBy    = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            if ($sortBy === 'name') {
                $query->join('users', 'agents.user_id', '=', 'users.id')
                      ->orderBy('users.name', $sortOrder)
                      ->select('agents.*');
            } else {
                $query->orderBy(in_array($sortBy, ['commission_earned', 'total_sales', 'account_status'])
                    ? $sortBy : 'created_at', $sortOrder);
            }

            $agents    = $query->paginate(15)->withQueryString();
            $countries = Country::orderBy('name')->get();
            $stats     = $this->getStats();

            return view('admin.agent.index', compact('agents', 'countries', 'stats'));

        } catch (\Exception $e) {
            Log::error('Admin Agent Index Error: ' . $e->getMessage());
            return redirect()->route('admin.dashboard.home')
                ->with('error', 'An error occurred while loading agents.');
        }
    }

    // ─── Create / Store ───────────────────────────────────────────────────────

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.agent.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => ['required', 'confirmed', Password::min(8)],
            'phone'           => 'required|string|max:20',
            'phone_code'      => 'required|string|max:10',
            'country_id'      => 'required|exists:countries,id',
            'city'            => 'required|string|max:100',
            'company_name'    => 'nullable|string|max:255',
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            $plainPassword = $request->password;

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($plainPassword),
            ]);

            $agentRole = \App\Models\Role::where('slug', 'agent')->firstOrCreate(
                ['slug' => 'agent'],
                ['name' => 'Agent', 'description' => 'Sales Agent']
            );
            $user->assignRole($agentRole);

            $agent = Agent::create([
                'user_id'           => $user->id,
                'country_id'        => $request->country_id,
                'phone'             => $request->phone,
                'phone_code'        => $request->phone_code,
                'city'              => $request->city,
                'company_name'      => $request->company_name,
                'commission_rate'   => $request->commission_rate,
                'account_status'    => 'active',
                'email_verified'    => true,
                'email_verified_at' => now(),
            ]);

            // Notification
            Notification::create([
                'title'   => 'Agent Account Created',
                'content' => 'Your AfriSellers agent account has been created. You can now log in.',
                'link_url'=> '/agent/dashboard',
                'user_id' => $user->id,
                'is_read' => false,
            ]);

            // Email — send plain password before it is hashed
            Mail::to($user->email)
                ->send(new AgentAccountCreatedMail($user->name, $user->email, $plainPassword));

            DB::commit();

            return redirect()->route('admin.agents.index')
                ->with('success', 'Agent account created and credentials sent by email.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent Create Error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(Agent $agent)
    {
        $agent->load(['user', 'country']);

        // Vendors linked to this agent with their business profile + products
        $vendors = Vendor::where('agent_id', $agent->user_id)
            ->with(['businessProfile.country', 'businessProfile.products', 'user'])
            ->get();

        // Vendors NOT yet linked to this agent (available to assign)
        $availableVendors = Vendor::whereNull('agent_id')
            ->with(['businessProfile', 'user'])
            ->get();

        $totalProducts = $vendors->sum(fn($v) => $v->businessProfile?->products()->count() ?? 0);

        return view('admin.agent.show', compact(
            'agent', 'vendors', 'availableVendors', 'totalProducts'
        ));
    }

    // ─── Edit / Update ────────────────────────────────────────────────────────

    public function edit(Agent $agent)
    {
        $agent->load('user', 'country');
        $countries = Country::orderBy('name')->get();
        return view('admin.agent.edit', compact('agent', 'countries'));
    }

    public function update(Request $request, Agent $agent)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $agent->user_id,
            'password'        => ['nullable', 'confirmed', Password::min(8)],
            'phone'           => 'required|string|max:20',
            'phone_code'      => 'required|string|max:10',
            'country_id'      => 'required|exists:countries,id',
            'city'            => 'required|string|max:100',
            'company_name'    => 'nullable|string|max:255',
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            $userData = ['name' => $request->name, 'email' => $request->email];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $agent->user->update($userData);

            $agent->update([
                'country_id'      => $request->country_id,
                'phone'           => $request->phone,
                'phone_code'      => $request->phone_code,
                'city'            => $request->city,
                'company_name'    => $request->company_name,
                'commission_rate' => $request->commission_rate,
            ]);

            DB::commit();

            return redirect()->route('admin.agents.show', $agent)
                ->with('success', 'Agent updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent Update Error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // ─── Assign Vendor to Agent ───────────────────────────────────────────────

    public function assignVendor(Request $request, Agent $agent)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        $vendor = Vendor::with(['businessProfile.country'])->findOrFail($request->vendor_id);

        if ($vendor->agent_id) {
            return back()->withErrors(['error' => 'This vendor is already assigned to another agent.']);
        }

        $vendor->update(['agent_id' => $agent->user_id]);

        $bp          = $vendor->businessProfile;
        $bizName     = $bp->business_name ?? 'Unknown Business';
        $city        = $bp->city ?? '';
        $countryName = $bp->country->name ?? '';

        // Notification to agent
        Notification::create([
            'title'   => 'New Vendor Assigned',
            'content' => "Vendor \"{$bizName}\" has been assigned to your account.",
            'link_url'=> '/agent/dashboard',
            'user_id' => $agent->user_id,
            'is_read' => false,
        ]);

        // Email to agent
        try {
            Mail::to($agent->user->email)
                ->send(new AgentVendorAssignedMail(
                    $agent->user->name,
                    $bizName,
                    $city,
                    $countryName
                ));
        } catch (\Exception $e) {
            Log::error('Agent vendor assigned email failed: ' . $e->getMessage());
        }

        return back()->with('success', "Vendor \"{$bizName}\" assigned to {$agent->user->name} successfully.");
    }

    // ─── Remove Vendor from Agent ─────────────────────────────────────────────

    public function removeVendor(Agent $agent, Vendor $vendor)
    {
        if ($vendor->agent_id !== $agent->user_id) {
            return back()->withErrors(['error' => 'This vendor is not assigned to this agent.']);
        }

        $bizName = $vendor->businessProfile->business_name ?? 'Unknown Business';

        $vendor->update(['agent_id' => null]);

        // Notification to agent
        Notification::create([
            'title'   => 'Vendor Removed',
            'content' => "Vendor \"{$bizName}\" has been removed from your account.",
            'link_url'=> '/agent/dashboard',
            'user_id' => $agent->user_id,
            'is_read' => false,
        ]);

        // Email to agent
        try {
            Mail::to($agent->user->email)
                ->send(new AgentVendorRemovedMail($agent->user->name, $bizName));
        } catch (\Exception $e) {
            Log::error('Agent vendor removed email failed: ' . $e->getMessage());
        }

        return back()->with('success', "Vendor \"{$bizName}\" removed from {$agent->user->name}.");
    }

    // ─── Status Actions ───────────────────────────────────────────────────────

    public function activate(Agent $agent)
    {
        try {
            $agent->activate();

            Notification::create([
                'title'      => 'Account Activated',
                'content'    => 'Your agent account has been activated. You can now start earning commissions.',
                'link_url'   => '/agent/dashboard',
                'user_id'    => $agent->user_id,
                'country_id' => $agent->country_id,
                'is_read'    => false,
            ]);

            return back()->with('success', 'Agent activated successfully.');
        } catch (\Exception $e) {
            Log::error('Agent Activation Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to activate agent.');
        }
    }

    public function suspend(Agent $agent)
    {
        try {
            $agent->suspend();

            Notification::create([
                'title'      => 'Account Suspended',
                'content'    => 'Your agent account has been suspended. Contact admin for assistance.',
                'user_id'    => $agent->user_id,
                'country_id' => $agent->country_id,
                'is_read'    => false,
            ]);

            return back()->with('success', 'Agent suspended successfully.');
        } catch (\Exception $e) {
            Log::error('Agent Suspension Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to suspend agent.');
        }
    }

    public function verifyEmail(Agent $agent)
    {
        try {
            $agent->verifyEmail();

            Notification::create([
                'title'   => 'Email Verified ✅',
                'content' => 'Your email address has been verified by the admin.',
                'user_id' => $agent->user_id,
                'is_read' => false,
            ]);

            return back()->with('success', 'Agent email verified.');
        } catch (\Exception $e) {
            Log::error('Agent Email Verify Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to verify email.');
        }
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function destroy(Agent $agent)
    {
        try {
            // Unlink all vendors first
            Vendor::where('agent_id', $agent->user_id)->update(['agent_id' => null]);

            if ($agent->user_id) {
                Notification::create([
                    'title'   => 'Agent Account Removed',
                    'content' => 'Your agent account has been removed from AfriSellers.',
                    'user_id' => $agent->user_id,
                    'is_read' => false,
                ]);
            }

            $agent->delete();

            return redirect()->route('admin.agents.index')
                ->with('success', 'Agent deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Agent Deletion Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete agent.');
        }
    }

    // ─── Print ────────────────────────────────────────────────────────────────

    public function print()
    {
        $agents = Agent::with(['user', 'country'])->get();
        $stats  = $this->getStats();
        return view('admin.agent.print', compact('agents', 'stats'));
    }

    // ─── Switch to Agent Dashboard ────────────────────────────────────────────

    public function switchToAgent(Agent $agent)
    {
        try {
            $user      = $agent->user;
            $agentRole = \App\Models\Role::where('slug', 'agent')->first();

            if (!$agentRole) {
                return response()->json(['success' => false, 'message' => 'Agent role not found.'], 404);
            }

            if (!$user->roles()->where('role_id', $agentRole->id)->exists()) {
                $user->roles()->attach($agentRole->id);
            }

            $token = \Illuminate\Support\Str::random(60);
            \Illuminate\Support\Facades\Cache::put(
                'agent_login_token_' . $token,
                $user->id,
                now()->addMinutes(5)
            );

            return response()->json([
                'success'   => true,
                'login_url' => route('auth.agent.token-login', ['token' => $token]),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function getStats(): array
    {
        $total    = Agent::count();
        $active   = Agent::where('account_status', 'active')->count();
        $pending  = Agent::where('account_status', 'pending')->count();
        $suspended= Agent::where('account_status', 'suspended')->count();
        $verified = Agent::where('email_verified', true)->count();

        return [
            'total'               => $total,
            'active'              => $active,
            'pending'             => $pending,
            'suspended'           => $suspended,
            'email_verified'      => $verified,
            'email_pending'       => Agent::where('email_verified', false)->count(),
            'total_commission'    => Agent::sum('commission_earned'),
            'total_sales'         => Agent::sum('total_sales'),
            'avg_commission_rate' => round(Agent::avg('commission_rate'), 2),
            'active_percentage'   => $total > 0 ? round(($active / $total) * 100, 1) : 0,
            'pending_percentage'  => $total > 0 ? round(($pending / $total) * 100, 1) : 0,
            'suspended_percentage'=> $total > 0 ? round(($suspended / $total) * 100, 1) : 0,
            'verified_percentage' => $total > 0 ? round(($verified / $total) * 100, 1) : 0,
            'today'               => Agent::whereDate('created_at', today())->count(),
            'this_week'           => Agent::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month'          => Agent::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];
    }
}
