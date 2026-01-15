<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('business_profile_id');
            $table->unsignedBigInteger('owner_id_document_id');
            $table->string('account_status')->default('active')->comment('inactive, active');
            $table->unsignedBigInteger('plan_id');
            $table->string('email_verification_token', 6)->default(Str::random(6));
            $table->boolean('email_verified')->default(true);
            $table->timestamp('email_verified_at')->default(now());
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
