<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('business_name');
            $table->string('phone');
            $table->string('country');
            $table->string('city');
            $table->string('business_registration_number')->unique();
            $table->string('business_registration_doc')->nullable();
            $table->string('owner_id_document')->nullable();
            $table->string('owner_full_name');
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->enum('account_status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
