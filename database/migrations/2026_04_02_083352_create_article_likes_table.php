<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleLikesTable extends Migration
{
    public function up()
    {
        Schema::create('article_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->timestamps();

            $table->unique(['article_id', 'ip_address']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_likes');
    }
}
