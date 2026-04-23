<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('plan_features', 'feature_id')) {
            Schema::table('plan_features', function (Blueprint $table) {
                $table->foreignId('feature_id')->nullable()->after('plan_id')->constrained('features')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('plan_features', 'feature_key')) {
            $keys = DB::table('plan_features')->distinct()->pluck('feature_key');

            foreach ($keys as $key) {
                if (! $key) {
                    continue;
                }
                $exists = DB::table('features')->where('feature_key', $key)->exists();
                if (! $exists) {
                    DB::table('features')->insert([
                        'name' => Str::headline(str_replace('_', ' ', $key)),
                        'slug' => Str::slug($key),
                        'feature_key' => $key,
                        'status' => 'active',
                        'is_supported' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            foreach (DB::table('plan_features')->get() as $row) {
                $featureId = DB::table('features')->where('feature_key', $row->feature_key)->value('id');
                if ($featureId) {
                    DB::table('plan_features')->where('id', $row->id)->update(['feature_id' => $featureId]);
                }
            }

            Schema::table('plan_features', function (Blueprint $table) {
                $table->dropForeign(['plan_id']);
            });

            Schema::table('plan_features', function (Blueprint $table) {
                $table->dropUnique(['plan_id', 'feature_key']);
            });

            Schema::table('plan_features', function (Blueprint $table) {
                $table->foreign('plan_id')->references('id')->on('membership_plans')->cascadeOnDelete();
            });

            Schema::table('plan_features', function (Blueprint $table) {
                $table->dropColumn('feature_key');
            });
        }

        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropForeign(['feature_id']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE plan_features MODIFY feature_id BIGINT UNSIGNED NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE plan_features ALTER COLUMN feature_id SET NOT NULL');
        }

        Schema::table('plan_features', function (Blueprint $table) {
            $table->foreign('feature_id')->references('id')->on('features')->cascadeOnDelete();
        });

        $hasPairUnique = false;
        if ($driver === 'mysql') {
            $hasPairUnique = collect(DB::select('SHOW INDEX FROM plan_features WHERE Key_name = ?', ['plan_features_plan_id_feature_id_unique']))->isNotEmpty();
        }
        if (! $hasPairUnique) {
            Schema::table('plan_features', function (Blueprint $table) {
                $table->unique(['plan_id', 'feature_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropUnique(['plan_id', 'feature_id']);
        });

        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropForeign(['feature_id']);
        });

        Schema::table('plan_features', function (Blueprint $table) {
            $table->string('feature_key')->after('plan_id');
        });

        foreach (DB::table('plan_features')->get() as $row) {
            $key = DB::table('features')->where('id', $row->feature_id)->value('feature_key');
            DB::table('plan_features')->where('id', $row->id)->update(['feature_key' => $key]);
        }

        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropColumn('feature_id');
        });

        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
        });

        Schema::table('plan_features', function (Blueprint $table) {
            $table->unique(['plan_id', 'feature_key']);
        });

        Schema::table('plan_features', function (Blueprint $table) {
            $table->foreign('plan_id')->references('id')->on('membership_plans')->cascadeOnDelete();
        });
    }
};
