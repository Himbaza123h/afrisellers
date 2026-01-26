<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            // Add referral_id if it doesn't exist
            if (!Schema::hasColumn('commissions', 'referral_id')) {
                $table->foreignId('referral_id')->nullable()->after('transaction_id')->constrained('referrals')->onDelete('set null');
            }

            // Add agent_id if it doesn't exist
            if (!Schema::hasColumn('commissions', 'agent_id')) {
                $table->foreignId('agent_id')->nullable()->after('referral_id')->constrained('users')->onDelete('set null');
            }

            // Add amount field if it doesn't exist (alias for commission_amount)
            if (!Schema::hasColumn('commissions', 'amount')) {
                $table->decimal('amount', 15, 2)->nullable()->after('agent_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            if (Schema::hasColumn('commissions', 'referral_id')) {
                $table->dropForeign(['referral_id']);
                $table->dropColumn('referral_id');
            }

            if (Schema::hasColumn('commissions', 'agent_id')) {
                $table->dropForeign(['agent_id']);
                $table->dropColumn('agent_id');
            }

            if (Schema::hasColumn('commissions', 'amount')) {
                $table->dropColumn('amount');
            }
        });
    }
};
