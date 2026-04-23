<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::table('configurations', function (Blueprint $table) {
        // Drop the old single-column unique index
        $table->dropUnique(['unique_id']);

        // Add composite unique: same unique_id allowed if country_id differs
        $table->unique(['unique_id', 'country_id'], 'configurations_unique_id_country_id_unique');
    });
}

public function down()
{
    Schema::table('configurations', function (Blueprint $table) {
        $table->dropUnique('configurations_unique_id_country_id_unique');
        $table->unique('unique_id');
    });
}
};
