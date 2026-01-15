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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('business_profile_id')->nullable()->constrained('business_profiles')->onDelete('set null');

            // Agent personal information
            $table->string('phone')->nullable();
            $table->string('phone_code')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('sex', ['male', 'female', 'other'])->nullable();

            // Agent business details
            $table->string('company_name')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_photo')->nullable();

            // Status and verification
            $table->enum('account_status', ['active', 'pending', 'suspended'])->default('pending');
            $table->boolean('email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_token')->nullable();

            // Agent performance metrics
            $table->integer('total_sales')->default(0);
            $table->decimal('commission_earned', 15, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(10.00); // Default 10% commission

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('country_id');
            $table->index('business_profile_id');
            $table->index('account_status');
            $table->index('email_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
