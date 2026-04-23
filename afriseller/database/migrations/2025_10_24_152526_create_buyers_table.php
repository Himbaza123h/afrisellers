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
        Schema::create('buyers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('phone', 20);
            $table->unsignedBigInteger('country_id');
            $table->string('phone_code', 10)->default('+250');
            $table->string('city', 100);
            $table->date('date_of_birth');
            $table->enum('sex', ['Male', 'Female', 'Other']);
            $table->enum('account_status', ['pending', 'active', 'suspended'])->default('pending');
            $table->string('email_verification_token', 6)->nullable();
            $table->boolean('email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyers');
    }
};
