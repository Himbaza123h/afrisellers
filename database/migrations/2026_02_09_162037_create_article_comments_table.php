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
        Schema::create('article_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('article_comments')->onDelete('cascade');

            // Comment content
            $table->string('commenter_name');
            $table->string('commenter_email')->nullable();
            $table->text('content');

            // Engagement
            $table->integer('likes_count')->default(0);

            // Moderation
            $table->enum('status', ['pending', 'approved', 'rejected', 'spam'])->default('approved');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('article_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_comments');
    }
};
