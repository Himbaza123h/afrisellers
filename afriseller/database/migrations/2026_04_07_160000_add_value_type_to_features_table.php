<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('features', function (Blueprint $table) {
            $table->string('value_type', 32)->default('text')->after('feature_key');
        });

        $map = config('membership_feature_keys', []);

        foreach (DB::table('features')->get() as $row) {
            $meta = $map[$row->feature_key] ?? null;
            $type = is_array($meta) ? ($meta['type'] ?? 'text') : 'text';

            if (! in_array($type, ['boolean', 'number', 'number_or_unlimited', 'text'], true)) {
                $type = 'text';
            }

            DB::table('features')->where('id', $row->id)->update(['value_type' => $type]);
        }
    }

    public function down(): void
    {
        Schema::table('features', function (Blueprint $table) {
            $table->dropColumn('value_type');
        });
    }
};
