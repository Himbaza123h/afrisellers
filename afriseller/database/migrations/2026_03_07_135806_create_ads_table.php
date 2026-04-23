<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Media
            $table->enum('media_type', ['image', 'video']);
            $table->string('media_path');           // stored file path
            $table->string('media_original_name')->nullable();
            $table->unsignedBigInteger('media_size')->nullable(); // bytes
            $table->string('thumbnail_path')->nullable();         // for video preview

            // Ad details
            $table->string('target_url')->nullable();             // click-through URL
            $table->enum('placement', ['homepage', 'sidebar', 'banner', 'popup', 'feed'])->default('feed');
            $table->enum('status', ['draft', 'active', 'paused', 'expired', 'rejected'])->default('draft');

            // Scheduling
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Stats
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);

            // Admin
            $table->boolean('is_admin_approved')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
