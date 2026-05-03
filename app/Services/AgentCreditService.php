<?php

namespace App\Services;

use App\Models\AgentCredit;
use App\Models\Credit;
use App\Models\CreditTransaction;
use Illuminate\Support\Facades\Log;

class AgentCreditService
{
    /**
     * Award credits to an agent.
     *
     * @param  int         $agentId           The agent's ID
     * @param  string      $creditType        The credit type key (matches `credits.type` column)
     * @param  string      $transactionType   The transaction type label stored in credit_transactions
     * @param  float|null  $fallbackAmount    Used when no matching Credit row is found (default: 5.0)
     * @return bool        true on success, false if agentId is null or an error occurs
     */
    public function reward(
        int $agentId,
        string $creditType,
        string $transactionType,
        float $fallbackAmount = 5.0
    ): bool {
        try {
            $creditEntry  = Credit::where('type', $creditType)->first();
            $creditAmount = $creditEntry ? (float) $creditEntry->value : $fallbackAmount;

            $agentCredit = AgentCredit::firstOrNew(['agent_id' => $agentId]);
            $agentCredit->total_credits = (float) ($agentCredit->total_credits ?? 0) + $creditAmount;
            $agentCredit->save();

            CreditTransaction::create([
                'agent_id'         => $agentId,
                'transaction_type' => $transactionType,
                'credits'          => $creditAmount,
            ]);

            Log::info('Agent credit awarded', [
                'agent_id'         => $agentId,
                'credit_type'      => $creditType,
                'transaction_type' => $transactionType,
                'amount'           => $creditAmount,
            ]);

            return true;

        } catch (\Throwable $e) {
            Log::error('AgentCreditService::reward failed', [
                'agent_id'         => $agentId,
                'credit_type'      => $creditType,
                'transaction_type' => $transactionType,
                'error'            => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Convenience wrapper — skips silently when agentId is null.
     * Safe to call without checking whether an agent exists.
     */
    public function rewardIfAgent(
        ?int $agentId,
        string $creditType,
        string $transactionType,
        float $fallbackAmount = 5.0
    ): bool {
        if (!$agentId) {
            return false;
        }

        return $this->reward($agentId, $creditType, $transactionType, $fallbackAmount);
    }
}
