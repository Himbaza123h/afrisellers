<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'closed'
        DB::statement("ALTER TABLE `r_f_qs` MODIFY COLUMN `status` ENUM('pending', 'accepted', 'rejected', 'closed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE `r_f_qs` MODIFY COLUMN `status` ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending'");
    }
};
