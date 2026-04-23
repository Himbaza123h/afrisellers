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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Basic article information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable(); // Short summary/excerpt
            $table->longText('content'); // Full article content

            // Category/Classification
            $table->string('category')->nullable(); // e.g., Agriculture, Export, Quality, etc.
            $table->json('tags')->nullable(); // Array of tags

            // Author information (if different from user)
            $table->string('author_name')->nullable();
            $table->string('author_title')->nullable(); // e.g., Senior Agricultural Expert
            $table->text('author_bio')->nullable();
            $table->string('author_avatar')->nullable();
            $table->json('author_social_links')->nullable(); // Twitter, LinkedIn, etc.

            // Article metrics
            $table->integer('views_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('reading_time_minutes')->nullable(); // Estimated reading time

            // Media
            $table->string('featured_image')->nullable();
            $table->string('featured_image_caption')->nullable();
            $table->json('gallery_images')->nullable(); // Array of additional images

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            // Publishing
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_comments')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index('user_id');
            $table->index('category');
            $table->index('status');
            $table->index('published_at');
            $table->index('is_featured');
            $table->index('views_count');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
