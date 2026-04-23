<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->string('extra_document')->nullable()->after('rejection_reason');
            $table->string('extra_document_original_name')->nullable()->after('extra_document');
            $table->timestamp('extra_document_uploaded_at')->nullable()->after('extra_document_original_name');
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'extra_document',
                'extra_document_original_name',
                'extra_document_uploaded_at',
            ]);
        });
    }
};
