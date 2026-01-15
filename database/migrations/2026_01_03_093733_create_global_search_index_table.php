<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_search_index', function (Blueprint $table) {
            $table->id();
            $table->string('searchable_type'); // Model name: User, Post, Product, etc.
            $table->unsignedBigInteger('searchable_id'); // ID of the actual record
            $table->string('title'); // Main heading/name
            $table->text('description')->nullable(); // Short description
            $table->text('search_content'); // Combined searchable content
            $table->string('url'); // Route to the resource
            $table->json('metadata')->nullable(); // Extra data like tags, categories
            $table->timestamps();

            // Indexes for better performance
            $table->index(['searchable_type', 'searchable_id']);
            $table->fullText(['title', 'search_content']); // Full-text search index
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_search_index');
    }
};
