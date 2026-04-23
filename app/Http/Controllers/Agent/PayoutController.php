<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentPayout;
use App\Models\Commission;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    // ─── Available balance (paid commissions minus already paid/approved payouts) ──
    private function availableBalance(): float
    {
        $agentId = auth()->id();

        $totalEarned = Commission::where('agent_id', $agentId)
            ->where('status', 'paid')
            ->sum('amount');

        $totalPaidOut = AgentPayout::where('agent_id', $agentId)
            ->whereIn('status', ['approved', 'processing', 'paid'])
            ->sum('amount');

        return max(0, (float) $totalEarned - (float) $totalPaidOut);
    }

    // ─── INDEX ────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $agentId = auth()->id();

        $payouts = AgentPayout::where('agent_id', $agentId)
            ->when($request->status, fn($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->date_from, fn($q) =>
                $q->whereDate('created_at', '>=', $request->date_from)
            )
            ->when($request->date_to, fn($q) =>
                $q->whereDate('created_at', '<=', $request->date_to)
            )
            ->when($request->search, fn($q) =>
                $q->where('payout_number', 'like', "%{$request->search}%")
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $base = AgentPayout::where('agent_id', $agentId);

        $stats = [
            'available'        => $this->availableBalance(),
            'total_paid'       => (clone $base)->where('status', 'paid')->sum('amount'),
            'total_pending'    => (clone $base)->where('status', 'pending')->sum('amount'),
            'total_processing' => (clone $base)->whereIn('status', ['approved', 'processing'])->sum('amount'),
            'count_pending'    => (clone $base)->where('status', 'pending')->count(),
            'count_paid'       => (clone $base)->where('status', 'paid')->count(),
        ];

        return view('agent.payouts.index', compact('payouts', 'stats'));
    }

    // ─── SHOW ─────────────────────────────────────────────────────────
    public function show($id)
    {
        $payout = AgentPayout::where('agent_id', auth()->id())
            ->findOrFail($id);

        return view('agent.payouts.show', compact('payout'));
    }

    // ─── REQUEST FORM ─────────────────────────────────────────────────
    public function request()
    {
        $available = $this->availableBalance();

        $paymentMethods = [
            'bank_transfer'  => 'Bank Transfer',
            'mobile_money'   => 'Mobile Money',
            'paypal'         => 'PayPal',
            'wise'           => 'Wise (TransferWise)',
            'crypto'         => 'Cryptocurrency',
        ];

        return view('agent.payouts.request', compact('available', 'paymentMethods'));
    }

    // ─── STORE ────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $available = $this->availableBalance();

        $validated = $request->validate([
            'amount'         => [
                'required', 'numeric', 'min:1',
                "max:{$available}",
            ],
            'payment_method' => 'required|in:bank_transfer,mobile_money,paypal,wise,crypto',
            'notes'          => 'nullable|string|max:1000',

            // Bank Transfer fields
            'bank_name'       => 'required_if:payment_method,bank_transfer|nullable|string|max:200',
            'account_name'    => 'required_if:payment_method,bank_transfer,mobile_money|nullable|string|max:200',
            'account_number'  => 'required_if:payment_method,bank_transfer,mobile_money|nullable|string|max:100',
            'swift_code'      => 'nullable|string|max:20',
            'routing_number'  => 'nullable|string|max:50',

            // Mobile Money fields
            'mobile_network'  => 'required_if:payment_method,mobile_money|nullable|string|max:100',
            'mobile_number'   => 'required_if:payment_method,mobile_money|nullable|string|max:30',

            // PayPal / Wise / Crypto
            'paypal_email'    => 'required_if:payment_method,paypal|nullable|email|max:200',
            'wise_email'      => 'required_if:payment_method,wise|nullable|email|max:200',
            'crypto_address'  => 'required_if:payment_method,crypto|nullable|string|max:300',
            'crypto_network'  => 'required_if:payment_method,crypto|nullable|string|max:100',
        ], [
            'amount.max' => "You can only request up to {$available} based on your available balance.",
        ]);

        // Build account_details based on method
        $accountDetails = match($validated['payment_method']) {
            'bank_transfer' => [
                'bank_name'      => $validated['bank_name'] ?? null,
                'account_name'   => $validated['account_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'swift_code'     => $validated['swift_code'] ?? null,
                'routing_number' => $validated['routing_number'] ?? null,
            ],
            'mobile_money' => [
                'account_name'   => $validated['account_name'] ?? null,
                'mobile_network' => $validated['mobile_network'] ?? null,
                'mobile_number'  => $validated['mobile_number'] ?? null,
            ],
            'paypal' => [
                'paypal_email' => $validated['paypal_email'] ?? null,
            ],
            'wise' => [
                'wise_email' => $validated['wise_email'] ?? null,
            ],
            'crypto' => [
                'crypto_address' => $validated['crypto_address'] ?? null,
                'crypto_network' => $validated['crypto_network'] ?? null,
            ],
            default => [],
        };

        $payout = AgentPayout::create([
            'agent_id'       => auth()->id(),
            'payout_number'  => AgentPayout::generatePayoutNumber(),
            'amount'         => $validated['amount'],
            'currency'       => 'USD',
            'payment_method' => $validated['payment_method'],
            'account_details'=> $accountDetails,
            'status'         => 'pending',
            'notes'          => $validated['notes'] ?? null,
        ]);

        return redirect()->route('agent.payouts.show', $payout->id)
            ->with('success', "Payout request {$payout->payout_number} submitted successfully. We will process it within 3–5 business days.");
    }

    // ─── CANCEL ───────────────────────────────────────────────────────
    public function cancel($id)
    {
        $payout = AgentPayout::where('agent_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $payout->update(['status' => 'cancelled']);

        return redirect()->route('agent.payouts.index')
            ->with('success', "Payout request {$payout->payout_number} has been cancelled.");
    }

    // ─── PRINT ────────────────────────────────────────────────────────
    public function print(Request $request)
    {
        $payouts = AgentPayout::where('agent_id', auth()->id())
            ->when($request->status, fn($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->date_from, fn($q) =>
                $q->whereDate('created_at', '>=', $request->date_from)
            )
            ->when($request->date_to, fn($q) =>
                $q->whereDate('created_at', '<=', $request->date_to)
            )
            ->latest()
            ->get();

        $totals = [
            'paid'    => $payouts->where('status', 'paid')->sum('amount'),
            'pending' => $payouts->where('status', 'pending')->sum('amount'),
            'count'   => $payouts->count(),
        ];

        return view('agent.payouts.print', compact('payouts', 'totals'));
    }
}
