<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add agent_id to vendors if it doesn't exist
        if (!Schema::hasColumn('vendors', 'agent_id')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->unsignedBigInteger('agent_id')->nullable()->after('user_id');
                $table->foreign('agent_id')->references('id')->on('users')->nullOnDelete();
            });
        }

        // Add stats columns to agents if they don't exist
        Schema::table('agents', function (Blueprint $table) {
            if (!Schema::hasColumn('agents', 'commission_earned')) {
                $table->decimal('commission_earned', 12, 2)->default(0)->after('commission_rate');
            }
            if (!Schema::hasColumn('agents', 'total_sales')) {
                $table->unsignedInteger('total_sales')->default(0)->after('commission_earned');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn('agent_id');
        });
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn(['commission_earned', 'total_sales']);
        });
    }
};
