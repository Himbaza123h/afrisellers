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
        Schema::table('agent_packages', function (Blueprint $table) {
            // add max_vendors column to agent_packages table
            $table->unsignedInteger('max_vendors')->default(1)->after('max_referrals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_packages', function (Blueprint $table) {
            // re,move max_vendors column from agent_packages table
            $table->dropColumn('max_vendors');
        });
    }
};
