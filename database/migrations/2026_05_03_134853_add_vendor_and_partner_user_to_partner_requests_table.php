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
            $table->unsignedBigInteger('vendor_user_id')->nullable()->after('id');
            $table->unsignedBigInteger('partner_user_id')->nullable()->after('vendor_user_id');
            // partner_user_id is filled by admin when they approve & create the partner User
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
