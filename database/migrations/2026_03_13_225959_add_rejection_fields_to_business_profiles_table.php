<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('business_profiles', function (Blueprint $table) {
        $table->longText('rejection_reason')->nullable()->after('verification_status');
        $table->longText('reason_reply')->nullable()->after('rejection_reason');
    });
}

public function down(): void
{
    Schema::table('business_profiles', function (Blueprint $table) {
        $table->dropColumn(['rejection_reason', 'reason_reply']);
    });
}
};
