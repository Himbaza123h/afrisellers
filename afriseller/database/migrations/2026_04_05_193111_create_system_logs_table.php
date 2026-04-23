<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->string('action');            // e.g. created, updated, deleted, login, failed
            $table->string('module')->nullable(); // e.g. vendors, documents, support, auth
            $table->string('entity_type')->nullable(); // model class name
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('description');       // human-readable summary
            $table->json('metadata')->nullable(); // any extra context
            $table->enum('level', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
