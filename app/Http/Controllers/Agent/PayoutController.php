<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentCredit;
use App\Models\AgentPayout;
use App\Models\CreditValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    // ─── Available balance (credit monetary value minus paid/approved payouts) ──
    private function availableBalance(): float
    {
        $agentId    = auth()->id();
        $credits    = (float) (AgentCredit::where('agent_id', $agentId)->value('total_credits') ?? 0);
        $multiplier = (float) (CreditValue::latest()->value('value') ?? 100);
        $monetary   = $credits * $multiplier;

        $totalPaidOut = (float) AgentPayout::where('agent_id', auth()->id())
            ->whereIn('status', ['approved', 'processing', 'paid'])
            ->sum('amount');

        return max(0, $monetary - $totalPaidOut);
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

        // ── Credit info ────────────────────────────────────────────────
        $agentCredit = AgentCredit::where('agent_id', $agentId)->first();
        $creditValue = CreditValue::latest()->first();
        $multiplier  = (float) ($creditValue?->value ?? 100);
        $totalCredits = (float) ($agentCredit?->total_credits ?? 0);

        $stats = [
            'available'        => $this->availableBalance(),
            'total_credits'    => $totalCredits,
            'monetary_value'   => $totalCredits * $multiplier,
            'multiplier'       => $multiplier,
            'total_paid'       => (clone $base)->where('status', 'paid')->sum('amount'),
            'total_pending'    => (clone $base)->where('status', 'pending')->sum('amount'),
            'total_processing' => (clone $base)->whereIn('status', ['approved', 'processing'])->sum('amount'),
            'total_rejected'   => (clone $base)->whereIn('status', ['rejected', 'cancelled'])->sum('amount'),
            'count_pending'    => (clone $base)->where('status', 'pending')->count(),
            'count_paid'       => (clone $base)->where('status', 'paid')->count(),
            'count_total'      => (clone $base)->count(),
        ];

        // ── Monthly chart — last 6 months (paid vs pending) ────────────
        $last6 = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        $paidRaw = AgentPayout::where('agent_id', $agentId)
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')->pluck('total', 'month');

        $pendingRaw = AgentPayout::where('agent_id', $agentId)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at,'%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')->pluck('total', 'month');

        $chartMonths  = $last6->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'))->values()->toArray();
        $chartPaid    = $last6->map(fn($m) => (float) ($paidRaw[$m] ?? 0))->values()->toArray();
        $chartPending = $last6->map(fn($m) => (float) ($pendingRaw[$m] ?? 0))->values()->toArray();

        // ── Status breakdown ───────────────────────────────────────────
        $statusBreakdown = AgentPayout::where('agent_id', $agentId)
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        return view('agent.payouts.index', compact(
            'payouts', 'stats',
            'chartMonths', 'chartPaid', 'chartPending',
            'statusBreakdown', 'agentCredit', 'multiplier'
        ));
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
        $available    = $this->availableBalance();
        $agentCredit  = AgentCredit::where('agent_id', auth()->id())->first();
        $creditValue  = CreditValue::latest()->first();
        $multiplier   = (float) ($creditValue?->value ?? 100);
        $totalCredits = (float) ($agentCredit?->total_credits ?? 0);

        $paymentMethods = [
            'bank_transfer' => 'Bank Transfer',
            'mobile_money'  => 'Mobile Money',
            'paypal'        => 'PayPal',
            'wise'          => 'Wise (TransferWise)',
            'crypto'        => 'Cryptocurrency',
        ];

        return view('agent.payouts.request', compact(
            'available', 'paymentMethods', 'totalCredits', 'multiplier'
        ));
    }

    // ─── STORE ────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $available = $this->availableBalance();

        $validated = $request->validate([
            'amount'          => ['required', 'numeric', 'min:1', "max:{$available}"],
            'payment_method'  => 'required|in:bank_transfer,mobile_money,paypal,wise,crypto',
            'notes'           => 'nullable|string|max:1000',
            'bank_name'       => 'required_if:payment_method,bank_transfer|nullable|string|max:200',
            'account_name'    => 'required_if:payment_method,bank_transfer,mobile_money|nullable|string|max:200',
            'account_number'  => 'required_if:payment_method,bank_transfer,mobile_money|nullable|string|max:100',
            'swift_code'      => 'nullable|string|max:20',
            'routing_number'  => 'nullable|string|max:50',
            'mobile_network'  => 'required_if:payment_method,mobile_money|nullable|string|max:100',
            'mobile_number'   => 'required_if:payment_method,mobile_money|nullable|string|max:30',
            'paypal_email'    => 'required_if:payment_method,paypal|nullable|email|max:200',
            'wise_email'      => 'required_if:payment_method,wise|nullable|email|max:200',
            'crypto_address'  => 'required_if:payment_method,crypto|nullable|string|max:300',
            'crypto_network'  => 'required_if:payment_method,crypto|nullable|string|max:100',
        ], [
            'amount.max' => "You can only request up to \${$available} based on your available balance.",
        ]);

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
            'paypal' => ['paypal_email' => $validated['paypal_email'] ?? null],
            'wise'   => ['wise_email'   => $validated['wise_email']   ?? null],
            'crypto' => [
                'crypto_address' => $validated['crypto_address'] ?? null,
                'crypto_network' => $validated['crypto_network'] ?? null,
            ],
            default => [],
        };

        $payout = AgentPayout::create([
            'agent_id'        => auth()->id(),
            'payout_number'   => AgentPayout::generatePayoutNumber(),
            'amount'          => $validated['amount'],
            'currency'        => 'USD',
            'payment_method'  => $validated['payment_method'],
            'account_details' => $accountDetails,
            'status'          => 'pending',
            'notes'           => $validated['notes'] ?? null,
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
        $agentId = auth()->id();

        $payouts = AgentPayout::where('agent_id', $agentId)
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->get();

        $totals = [
            'paid'      => $payouts->where('status', 'paid')->sum('amount'),
            'pending'   => $payouts->where('status', 'pending')->sum('amount'),
            'count'     => $payouts->count(),
            'available' => $this->availableBalance(),
        ];

        return view('agent.payouts.print', compact('payouts', 'totals'));
    }
}
