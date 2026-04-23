<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_media', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // friendly display name
            $table->string('original_name');                 // uploaded filename
            $table->string('file_path');                     // storage path
            $table->string('disk')->default('public');
            $table->string('mime_type', 100);
            $table->enum('type', ['image', 'gif', 'video', 'document']);
            $table->unsignedBigInteger('file_size');         // bytes
            $table->string('thumbnail_path')->nullable();    // for video posters
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_media');
    }
};
