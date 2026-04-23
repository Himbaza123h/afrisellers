<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Login History Table
        if (!Schema::hasTable('login_history')) {
            Schema::create('login_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('ip_address')->nullable();
                $table->string('country')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('login_at');
                $table->timestamps();
            });
        }

        // Failed Login Attempts Table
        if (!Schema::hasTable('failed_login_attempts')) {
            Schema::create('failed_login_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('email')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamp('attempted_at');
                $table->timestamps();
            });
        }

        // Security Events Table
        if (!Schema::hasTable('security_events')) {
            Schema::create('security_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('event');
                $table->string('ip_address')->nullable();
                $table->string('location')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        // Add 2FA columns to users table if they don't exist
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false);
            }
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->string('two_factor_secret')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('security_events');
        Schema::dropIfExists('failed_login_attempts');
        Schema::dropIfExists('login_history');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_enabled', 'two_factor_secret']);
        });
    }
};
