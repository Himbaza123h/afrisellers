<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('partner_requests', function (Blueprint $table) {
        $table->id();
        $table->string('company_name');
        $table->string('contact_name');
        $table->string('email');
        $table->string('phone')->nullable();
        $table->string('website_url')->nullable();
        $table->string('industry')->nullable();
        $table->string('country')->nullable();
        $table->string('partner_type')->nullable(); // what type they want to be
        $table->longText('message')->nullable(); // why they want to partner
        $table->string('logo')->nullable(); // company logo upload
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->text('admin_notes')->nullable(); // admin internal notes
        $table->timestamp('reviewed_at')->nullable();
        $table->unsignedBigInteger('reviewed_by')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}

public function down(): void
{
    Schema::dropIfExists('partner_requests');
}
};
