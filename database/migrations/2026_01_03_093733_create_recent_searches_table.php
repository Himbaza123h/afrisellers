<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recent_searches', function (Blueprint $table) {
            $table->id();
            $table->string('search_query'); // What the user searched for
            $table->integer('result_count')->default(0); // How many results were found
            $table->foreignId('clicked_result_id')->nullable()->constrained('global_search_index')->onDelete('set null'); // Which result was clicked
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Optional: track by user
            $table->integer('search_count')->default(1); // How many times this query was searched
            $table->timestamps();

            // Indexes
            $table->index('search_query');
            $table->index('user_id');
            $table->index('search_count'); // For popular searches
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recent_searches');
    }
};
