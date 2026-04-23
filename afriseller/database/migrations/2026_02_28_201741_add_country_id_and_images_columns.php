<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
// database/migrations/2024_01_01_000002_add_country_id_and_images_columns.php
public function up(): void
{
    Schema::table('configurations', function (Blueprint $table) {
        $table->unsignedBigInteger('country_id')->nullable()->after('is_active');
        $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
    });

    Schema::table('ui_sections', function (Blueprint $table) {
        $table->json('images')->nullable()->after('manual_items');
        $table->unsignedSmallInteger('max_images')->default(20)->after('images');
    });
}

public function down(): void
{
    Schema::table('configurations', function (Blueprint $table) {
        $table->dropForeign(['country_id']);
        $table->dropColumn('country_id');
    });
    Schema::table('ui_sections', function (Blueprint $table) {
        $table->dropColumn(['images', 'max_images']);
    });
}
};
