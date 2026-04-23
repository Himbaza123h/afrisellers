<?php
// database/migrations/2026_02_14_000003_create_messages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->nullable()->constrained('message_groups')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->string('type')->default('text'); // text, image, file, system
            $table->json('attachments')->nullable();
            $table->foreignId('reply_to')->nullable()->constrained('messages')->onDelete('set null');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sender_id', 'receiver_id']);
            $table->index('group_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
