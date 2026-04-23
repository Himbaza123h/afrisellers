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
        // add partner_request_id to partners table
        Schema::table('partners', function (Blueprint $table) {
            $table->unsignedBigInteger('partner_request_id')->nullable()->after('id');
            $table->foreign('partner_request_id')->references('id')->on('partner_requests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // remove partner_request_id from partners table
        Schema::table('partners', function (Blueprint $table) {
            $table->dropForeign(['partner_request_id']);
            $table->dropColumn('partner_request_id');
        });
    }
};
