<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentPackage;
use App\Models\AgentSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────
    public function index()
    {
        $agentId = auth()->id();

        $current = AgentSubscription::where('agent_id', $agentId)
            ->active()
            ->with('package')
            ->first();

        $history = AgentSubscription::where('agent_id', $agentId)
            ->with('package')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_spent' => AgentSubscription::where('agent_id', $agentId)
                                ->where('payment_status', 'paid')
                                ->sum('amount_paid'),
            'total_subs'  => AgentSubscription::where('agent_id', $agentId)->count(),
            'active'      => AgentSubscription::where('agent_id', $agentId)->active()->count(),
        ];

        return view('agent.subscriptions.index', compact('current', 'history', 'stats'));
    }

    // ─── PLANS ────────────────────────────────────────────────
    public function plans()
    {
        $packages = AgentPackage::active()->ordered()->get();

        $current = AgentSubscription::where('agent_id', auth()->id())
            ->active()
            ->with('package')
            ->first();

        return view('agent.subscriptions.plans', compact('packages', 'current'));
    }

    // ─── CURRENT ──────────────────────────────────────────────
    public function current()
    {
        $subscription = AgentSubscription::where('agent_id', auth()->id())
            ->active()
            ->with('package')
            ->first();

        return view('agent.subscriptions.current', compact('subscription'));
    }

    // ─── HISTORY ──────────────────────────────────────────────
    public function history()
    {
        $history = AgentSubscription::where('agent_id', auth()->id())
            ->with('package')
            ->latest()
            ->paginate(15);

        return view('agent.subscriptions.history', compact('history'));
    }

    // ─── PRINT ────────────────────────────────────────────────
    public function print()
    {
        $history = AgentSubscription::where('agent_id', auth()->id())
            ->with('package')
            ->latest()
            ->get();

        return view('agent.subscriptions.print', compact('history'));
    }

    // ─── SUBSCRIBE ────────────────────────────────────────────
    public function subscribe(Request $request, $plan)
    {
        $package = AgentPackage::active()->findOrFail($plan);
        $agentId = auth()->id();

        // Cancel any existing active subscription
        AgentSubscription::where('agent_id', $agentId)
            ->active()
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        $request->validate([
            'payment_method' => 'required|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $sub = AgentSubscription::create([
                'agent_id'        => $agentId,
                'package_id'      => $package->id,
                'amount_paid'     => $package->price,
                'status'          => 'active',
                'starts_at'       => now(),
                'expires_at'      => now()->addDays($package->duration_days),
                'payment_method'  => $request->payment_method,
                'payment_status'  => 'paid',
                'transaction_id'  => 'TXN-' . strtoupper(uniqid()),
                'auto_renew'      => $request->boolean('auto_renew'),
            ]);

            DB::commit();

            return redirect()->route('agent.subscriptions.index')
                ->with('success', "Successfully subscribed to the <strong>{$package->name}</strong> plan!");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Subscription failed: ' . $e->getMessage());
        }
    }

    // ─── UPGRADE ──────────────────────────────────────────────
    public function upgrade(Request $request)
    {
        $request->validate(['package_id' => 'required|exists:agent_packages,id']);

        $agentId  = auth()->id();
        $package  = AgentPackage::active()->findOrFail($request->package_id);
        $current  = AgentSubscription::where('agent_id', $agentId)->active()->first();

        if ($current) {
            $current->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        }

        AgentSubscription::create([
            'agent_id'       => $agentId,
            'package_id'     => $package->id,
            'amount_paid'    => $package->price,
            'status'         => 'active',
            'starts_at'      => now(),
            'expires_at'     => now()->addDays($package->duration_days),
            'payment_method' => $current?->payment_method ?? 'manual',
            'payment_status' => 'paid',
            'transaction_id' => 'TXN-' . strtoupper(uniqid()),
            'auto_renew'     => $current?->auto_renew ?? false,
        ]);

        return redirect()->route('agent.subscriptions.index')
            ->with('success', "Upgraded to <strong>{$package->name}</strong> successfully!");
    }

    // ─── DOWNGRADE ────────────────────────────────────────────
    public function downgrade(Request $request)
    {
        // Same flow as upgrade; the label is different in UX only
        return $this->upgrade($request);
    }

    // ─── RENEW ────────────────────────────────────────────────
    public function renew(Request $request)
    {
        $agentId = auth()->id();
        $sub = AgentSubscription::where('agent_id', $agentId)
            ->latest()
            ->with('package')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $newSub = AgentSubscription::create([
                'agent_id'        => $agentId,
                'package_id'      => $sub->package_id,
                'amount_paid'     => $sub->package->price,
                'status'          => 'active',
                'starts_at'       => now(),
                'expires_at'      => now()->addDays($sub->package->duration_days),
                'payment_method'  => $sub->payment_method,
                'payment_status'  => 'paid',
                'transaction_id'  => 'TXN-' . strtoupper(uniqid()),
                'auto_renew'      => $sub->auto_renew,
                'last_renewed_at' => now(),
            ]);

            // Mark old as expired
            $sub->update(['status' => 'expired']);

            DB::commit();

            return redirect()->route('agent.subscriptions.index')
                ->with('success', 'Subscription renewed successfully!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Renewal failed: ' . $e->getMessage());
        }
    }

    // ─── CANCEL ───────────────────────────────────────────────
    public function cancel(Request $request)
    {
        $sub = AgentSubscription::where('agent_id', auth()->id())
            ->active()
            ->firstOrFail();

        $sub->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew'   => false,
            'notes'        => $request->input('reason', 'Cancelled by agent'),
        ]);

        return redirect()->route('agent.subscriptions.index')
            ->with('success', 'Your subscription has been cancelled. Access continues until ' . $sub->expires_at->format('M d, Y') . '.');
    }

    // ─── RESUME ───────────────────────────────────────────────
    public function resume()
    {
        $sub = AgentSubscription::where('agent_id', auth()->id())
            ->where('status', 'cancelled')
            ->where('expires_at', '>', now())
            ->latest()
            ->firstOrFail();

        $sub->update([
            'status'       => 'active',
            'cancelled_at' => null,
        ]);

        return redirect()->route('agent.subscriptions.index')
            ->with('success', 'Subscription resumed successfully!');
    }

    // ─── TOGGLE AUTO-RENEW ────────────────────────────────────
    public function toggleAutoRenew()
    {
        $sub = AgentSubscription::where('agent_id', auth()->id())
            ->active()
            ->firstOrFail();

        $sub->update(['auto_renew' => !$sub->auto_renew]);

        $label = $sub->auto_renew ? 'enabled' : 'disabled';

        return back()->with('success', "Auto-renew {$label}.");
    }

    // ─── INVOICES LIST ────────────────────────────────────────
    public function invoices()
    {
        $invoices = AgentSubscription::where('agent_id', auth()->id())
            ->with('package')
            ->latest()
            ->paginate(15);

        return view('agent.subscriptions.invoices', compact('invoices'));
    }

    // ─── SINGLE INVOICE ───────────────────────────────────────
    public function invoice($subscription)
    {
        $sub = AgentSubscription::where('agent_id', auth()->id())
            ->with('package')
            ->findOrFail($subscription);

        return view('agent.subscriptions.invoice', compact('sub'));
    }

    // ─── DOWNLOAD INVOICE ─────────────────────────────────────
    public function downloadInvoice($subscription)
    {
        // Redirect to the printable invoice view (user can print-to-PDF)
        return redirect()->route('agent.subscriptions.invoice', $subscription);
    }

    // ─── PLAN PDF ─────────────────────────────────────────────
    public function planPdf($subscription)
    {
        $sub = AgentSubscription::where('agent_id', auth()->id())
            ->with('package')
            ->findOrFail($subscription);

        return view('agent.subscriptions.plan-pdf', compact('sub'));
    }
}
