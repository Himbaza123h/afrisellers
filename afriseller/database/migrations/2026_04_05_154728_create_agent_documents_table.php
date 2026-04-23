<?php
// php artisan make:migration create_agent_documents_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_name');            // original filename
            $table->string('file_path');            // stored path on disk
            $table->string('file_type');            // mime type
            $table->unsignedBigInteger('file_size');// bytes
            $table->string('category')->default('other');
            // contract | invoice | identity | agreement | report | license | other
            $table->json('tags')->nullable();
            $table->boolean('is_shared')->default(false); // shared with assigned vendors
            $table->timestamp('expires_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_documents');
    }
};
