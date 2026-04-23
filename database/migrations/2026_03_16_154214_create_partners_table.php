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
    Schema::create('partners', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('logo')->nullable(); // image or gif path
        $table->string('website_url')->nullable();
        $table->string('industry')->nullable();
        $table->string('partner_type')->nullable(); // e.g. Global Partner, Banking Partner
        $table->longText('description')->nullable(); // rich text
        $table->integer('sort_order')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        $table->softDeletes();
    });
}

public function down(): void
{
    Schema::dropIfExists('partners');
}
};
