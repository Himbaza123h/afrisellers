<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->string('payout_number')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('payment_method');         // bank_transfer, mobile_money, paypal …
            $table->json('account_details');           // { account_name, account_number, bank, … }
            $table->enum('status', ['pending', 'approved', 'processing', 'paid', 'rejected', 'cancelled'])
                  ->default('pending');
            $table->text('notes')->nullable();         // agent note at request time
            $table->text('admin_notes')->nullable();   // admin response
            $table->timestamp('processed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['agent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_payouts');
    }
};
