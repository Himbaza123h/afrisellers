<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('partner_requests', function (Blueprint $table) {
        // 1. Basic Company Info
        $table->string('trading_name')->nullable()->after('company_name');
        $table->string('registration_number')->nullable()->after('trading_name');
        $table->text('physical_address')->nullable()->after('country');

        // 2. Branding & Content
        $table->string('cover_image')->nullable();
        $table->string('short_description')->nullable();
        $table->text('full_description')->nullable();
        $table->string('promo_video_url')->nullable();

        // 3. Contact Person
        $table->string('contact_position')->nullable()->after('contact_name');
        $table->string('whatsapp')->nullable()->after('phone');

        // 4. Social Media
        $table->string('facebook_url')->nullable();
        $table->string('instagram_url')->nullable();
        $table->string('twitter_url')->nullable();
        $table->string('linkedin_url')->nullable();
        $table->string('youtube_url')->nullable();
        $table->string('tiktok_url')->nullable();

        // 5. Business Type
        $table->string('business_type')->nullable(); // Corporation, SME, Startup

        // 6. Operations
        $table->integer('branches_count')->nullable();
        $table->string('target_market')->nullable(); // Individuals, Businesses, Both
        $table->json('countries_of_operation')->nullable();
    });
}

public function down(): void
{
    Schema::table('partner_requests', function (Blueprint $table) {
        $table->dropColumn([
            'trading_name', 'registration_number', 'physical_address',
            'cover_image', 'short_description', 'full_description', 'promo_video_url',
            'contact_position', 'whatsapp',
            'facebook_url', 'instagram_url', 'twitter_url',
            'linkedin_url', 'youtube_url', 'tiktok_url',
            'business_type', 'branches_count', 'target_market', 'countries_of_operation',
        ]);
    });
}
};
