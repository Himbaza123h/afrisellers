<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('regional_admin')->default(false)->after('two_factor_secret');
            $table->boolean('country_admin')->default(false)->after('regional_admin');
            $table->boolean('agent')->default(false)->after('country_admin');
            $table->unsignedBigInteger('regional_id')->nullable()->after('agent');
            $table->unsignedBigInteger('country_id')->nullable()->after('regional_id');

            // Add foreign keys if you have regions and countries tables
            $table->foreign('regional_id')->references('id')->on('regions')->onDelete('set null');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['regional_admin', 'country_admin', 'agent', 'regional_id', 'country_id']);
        });
    }
};
