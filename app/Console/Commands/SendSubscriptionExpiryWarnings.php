<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionExpiringMail;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendSubscriptionExpiryWarnings extends Command
{
    protected $signature   = 'subscriptions:expiry-warnings';
    protected $description = 'Send email warnings to vendors whose subscription expires in 2 days';

    public function handle(): void
    {
        $subscriptions = Subscription::expiringSoon(2)
            ->with(['plan', 'seller'])
            ->get();

        $this->info("Found {$subscriptions->count()} subscription(s) expiring within 2 days");

        foreach ($subscriptions as $subscription) {
            $user = $subscription->seller;

            if (!$user || !$user->email) {
                Log::warning('[ExpiryWarning] No user/email for subscription', ['id' => $subscription->id]);
                $this->warn("⚠ Skipped subscription ID {$subscription->id} — no user/email");
                continue;
            }

            $daysLeft = max(1, (int) ceil(now()->floatDiffInDays($subscription->ends_at, false)));

            try {
                Mail::to($user->email)->send(new SubscriptionExpiringMail(
                    userName:   $user->name,
                    planName:   $subscription->plan->name,
                    expiryDate: $subscription->ends_at->format('M d, Y'),
                    daysLeft:   $daysLeft,
                ));

                Log::info('[ExpiryWarning] Email sent', [
                    'user_id' => $user->id,
                    'email'   => $user->email,
                    'plan'    => $subscription->plan->name,
                    'ends_at' => $subscription->ends_at,
                ]);

                $this->info("✓ Sent to {$user->email} ({$daysLeft} day(s) left)");

            } catch (\Exception $e) {
                Log::error('[ExpiryWarning] Failed to send email', [
                    'user_id' => $user->id,
                    'error'   => $e->getMessage(),
                ]);
                $this->error("✗ Failed for {$user->email}: {$e->getMessage()}");
            }
        }

        $this->info('Done.');
    }
}
