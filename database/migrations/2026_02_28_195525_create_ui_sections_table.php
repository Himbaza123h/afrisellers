<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ui_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // e.g. "Hero Section"
            $table->string('section_key')->unique();         // e.g. "hero_section"
            $table->boolean('is_active')->default(true);
            $table->boolean('is_slide')->default(false);
            $table->boolean('is_fade')->default(false);
            $table->boolean('is_flip')->default(false);
            $table->unsignedTinyInteger('number_items')->default(4); // max 8
            $table->boolean('allow_manual')->default(false); // can items be added manually?
            $table->json('manual_items')->nullable();         // manually added item IDs/data
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ui_sections');
    }
};
