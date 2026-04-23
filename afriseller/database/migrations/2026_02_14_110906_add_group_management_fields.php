<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('message_groups', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('is_active');
            $table->string('invite_code', 10)->unique()->nullable()->after('is_locked');
            $table->timestamp('invite_code_expires_at')->nullable()->after('invite_code');
        });
    }

    public function down()
    {
        Schema::table('message_groups', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'invite_code', 'invite_code_expires_at']);
        });
    }
};
