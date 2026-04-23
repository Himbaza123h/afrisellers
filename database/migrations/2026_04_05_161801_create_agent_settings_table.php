<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // General
            $table->string('timezone')->default('UTC');
            $table->string('language')->default('en');
            $table->string('currency')->default('USD');
            $table->string('date_format')->default('M d, Y');

            // Notifications
            $table->boolean('notify_email')->default(true);
            $table->boolean('notify_new_vendor')->default(true);
            $table->boolean('notify_commission')->default(true);
            $table->boolean('notify_ticket_reply')->default(true);
            $table->boolean('notify_payout')->default(true);
            $table->boolean('notify_expiry')->default(true);

            // Payment
            $table->string('payout_method')->nullable();  // bank | mobile_money | paypal
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('mobile_money_number')->nullable();
            $table->string('mobile_money_provider')->nullable();
            $table->string('paypal_email')->nullable();

            // Commission
            $table->string('commission_payout_threshold')->default('100');
            $table->string('commission_payout_frequency')->default('monthly');

            // Security
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_settings');
    }
};
