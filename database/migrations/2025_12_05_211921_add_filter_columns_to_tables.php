<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add columns to business_profiles table
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->boolean('is_verified_pro')->default(false)->after('is_admin_verified');
            $table->integer('response_time')->nullable()->comment('Response time in hours')->after('is_verified_pro');
        });

        // Add columns to products table
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('paid_samples')->default(false)->after('is_admin_verified');
            $table->boolean('customizable')->default(false)->after('paid_samples');
            $table->boolean('eco_friendly')->default(false)->after('customizable');
            $table->boolean('ready_to_ship')->default(false)->after('eco_friendly');
            $table->boolean('free_shipping')->default(false)->after('ready_to_ship');
            $table->integer('dispatch_days')->nullable()->after('free_shipping');
        });
    }

    public function down()
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn(['is_verified_pro', 'response_time']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'paid_samples',
                'customizable',
                'eco_friendly',
                'ready_to_ship',
                'free_shipping',
                'dispatch_days'
            ]);
        });
    }
};
