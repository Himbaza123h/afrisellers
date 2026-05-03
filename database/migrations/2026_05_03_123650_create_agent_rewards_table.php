<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('target_id')->constrained('targets')->onDelete('cascade');
            $table->string('period_key');         // e.g. "2025-05" monthly, "2025-W20" weekly, "2025" yearly
            $table->decimal('credits_awarded', 10, 2);
            $table->enum('status', ['pending', 'claimed'])->default('pending');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // The critical integrity constraint: one reward per agent per target per period
            $table->unique(['agent_id', 'target_id', 'period_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_rewards');
    }
};
