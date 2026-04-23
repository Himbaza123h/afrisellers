<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partner_requests', function (Blueprint $table) {
            $table->unsignedInteger('presence_countries')->nullable()->after('country');
            $table->unsignedSmallInteger('established')->nullable()->after('presence_countries');
            $table->text('about_us')->nullable()->after('established');
            $table->json('services')->nullable()->after('about_us');
            $table->string('intro')->nullable()->after('services'); // path to image or video
        });
    }

    public function down(): void
    {
        Schema::table('partner_requests', function (Blueprint $table) {
            $table->dropColumn(['presence_countries', 'established', 'about_us', 'services', 'intro']);
        });
    }
};
