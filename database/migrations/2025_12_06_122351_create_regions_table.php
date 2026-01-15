<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create regions table
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Add region_id to countries table
        Schema::table('countries', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('id')->constrained('regions')->nullOnDelete();
        });

        // Insert regions data
        DB::table('regions')->insert([
            ['name' => 'East Africa', 'code' => 'EA', 'description' => 'East African region', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'West Africa', 'code' => 'WA', 'description' => 'West African region', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Southern Africa', 'code' => 'SA', 'description' => 'Southern African region', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'North Africa', 'code' => 'NA', 'description' => 'North African region', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Central Africa', 'code' => 'CA', 'description' => 'Central African region', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Update countries with their regions
        $regionMapping = [
            'East Africa' => ['Rwanda', 'Kenya', 'Tanzania', 'Uganda', 'Ethiopia'],
            'West Africa' => ['Ghana', 'Nigeria', 'Senegal', 'Ivory Coast', 'Mali', 'Burkina Faso', 'Niger'],
            'Southern Africa' => ['South Africa', 'Zimbabwe', 'Zambia', 'Mozambique', 'Angola', 'Malawi', 'Madagascar'],
            'North Africa' => ['Egypt', 'Morocco', 'Tunisia', 'Algeria', 'Sudan'],
            'Central Africa' => ['Cameroon'],
        ];

        foreach ($regionMapping as $regionName => $countries) {
            $region = DB::table('regions')->where('name', $regionName)->first();
            if ($region) {
                DB::table('countries')
                    ->whereIn('name', $countries)
                    ->update(['region_id' => $region->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });

        Schema::dropIfExists('regions');
    }
};
