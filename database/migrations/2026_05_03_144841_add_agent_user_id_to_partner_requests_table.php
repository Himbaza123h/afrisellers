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
    Schema::table('partner_requests', function (Blueprint $table) {
        $table->unsignedBigInteger('registered_by_agent_id')->nullable()->after('vendor_user_id');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_requests', function (Blueprint $table) {
            //
        });
    }
};
