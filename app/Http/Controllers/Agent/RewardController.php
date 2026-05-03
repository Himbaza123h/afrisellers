<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentCredit;
use App\Models\AgentReward;
use App\Models\CreditTransaction;
use App\Models\Target;
use Illuminate\Support\Facades\DB;

class RewardController extends Controller
{
    public function index()
    {
        $agentId = auth()->id();

        // Check and auto-award any targets reached this period
        $this->checkAndAwardTargets($agentId);

        $rewards = AgentReward::where('agent_id', $agentId)
            ->with('target')
            ->latest()
            ->paginate(20);

        $stats = [
            'pending_rewards' => AgentReward::where('agent_id', $agentId)->where('status', 'pending')->sum('credits_awarded'),
            'claimed_rewards' => AgentReward::where('agent_id', $agentId)->where('status', 'claimed')->sum('credits_awarded'),
            'total_rewards'   => AgentReward::where('agent_id', $agentId)->sum('credits_awarded'),
            'pending_count'   => AgentReward::where('agent_id', $agentId)->where('status', 'pending')->count(),
        ];

        return view('agent.rewards.index', compact('rewards', 'stats'));
    }

    public function claim($rewardId)
    {
        $reward = AgentReward::where('agent_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($rewardId);

        DB::transaction(function () use ($reward) {
            // Add credits to agent balance atomically
            $agentCredit = AgentCredit::firstOrNew(['agent_id' => $reward->agent_id]);
            $agentCredit->total_credits = (float) ($agentCredit->total_credits ?? 0) + (float) $reward->credits_awarded;
            $agentCredit->save();

            // Record the transaction
            CreditTransaction::create([
                'agent_id'         => $reward->agent_id,
                'transaction_type' => 'target_reward_claimed',
                'credits'          => $reward->credits_awarded,
            ]);

            // Mark reward as claimed — this is the integrity lock
            $reward->update([
                'status'     => 'claimed',
                'claimed_at' => now(),
            ]);
        });

        return back()->with('success', number_format($reward->credits_awarded, 2) . ' bonus credits have been added to your balance!');
    }

    // ── Internal: check all active targets and award if reached ───────
    private function checkAndAwardTargets(int $agentId): void
    {
        $targets = Target::where(function ($q) {
            $q->whereNull('end_at')->orWhereDate('end_at', '>=', now());
        })->get();

        foreach ($targets as $target) {
            $periodKey   = AgentReward::periodKeyFor($target->target_type);
            $agentEarned = $this->getAgentEarnedForPeriod($agentId, $target->target_type);

            if ($agentEarned < $target->target_amount) {
                continue; // Not reached yet
            }

            // Try to insert — the unique constraint prevents double-awarding
            try {
                AgentReward::firstOrCreate(
                    [
                        'agent_id'   => $agentId,
                        'target_id'  => $target->id,
                        'period_key' => $periodKey,
                    ],
                    [
                        'credits_awarded' => (float) $target->prize,
                        'status'          => 'pending',
                    ]
                );
            } catch (\Throwable $e) {
                // Unique constraint hit = already awarded, safe to ignore
            }
        }
    }

    private function getAgentEarnedForPeriod(int $agentId, string $targetType): float
    {
        $query = CreditTransaction::where('agent_id', $agentId)
            ->where('credits', '>', 0) // only positive (earning) transactions
            ->whereNotIn('transaction_type', ['target_reward_claimed']); // don't count reward claims toward the target

        return match ($targetType) {
            'monthly' => (float) $query
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('credits'),
            'weekly' => (float) $query
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('credits'),
            'yearly' => (float) $query
                ->whereYear('created_at', now()->year)
                ->sum('credits'),
            default => 0.0,
        };
    }
}
