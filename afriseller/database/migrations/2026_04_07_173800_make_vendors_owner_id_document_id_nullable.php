<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE `vendors` MODIFY `owner_id_document_id` BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `vendors` MODIFY `owner_id_document_id` BIGINT UNSIGNED NOT NULL');
    }
};
