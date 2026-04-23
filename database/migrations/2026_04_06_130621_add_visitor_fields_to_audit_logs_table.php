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
    Schema::table('audit_logs', function (Blueprint $table) {
        $table->string('country')->nullable()->after('user_agent');
        $table->string('city')->nullable()->after('country');
        $table->string('browser')->nullable()->after('city');
        $table->string('platform')->nullable()->after('browser');
        $table->string('url', 500)->nullable()->after('platform');
        $table->string('method', 10)->nullable()->after('url');
        $table->string('referer', 500)->nullable()->after('method');
    });
}

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::table('audit_logs', function (Blueprint $table) {
        $table->dropColumn(['country','city','browser','platform','url','method','referer']);
    });
}
};
