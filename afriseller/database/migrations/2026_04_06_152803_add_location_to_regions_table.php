<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regional_admins', function (Blueprint $table) {
            $table->string('location')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('regional_admins', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }
};
