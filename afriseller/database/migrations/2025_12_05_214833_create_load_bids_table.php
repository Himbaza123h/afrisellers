<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('load_bids', function (Blueprint $table) {
            $table->id();
            $table->string('bid_number')->unique();
            $table->foreignId('load_id')->constrained()->onDelete('cascade');
            $table->foreignId('transporter_id')->constrained()->onDelete('cascade');
            $table->decimal('bid_amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->integer('estimated_delivery_days')->nullable();
            $table->text('proposal')->nullable();
            $table->json('vehicle_details')->nullable();
            $table->json('insurance_details')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamp('valid_until')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['load_id', 'status']);
            $table->index(['transporter_id', 'status']);
            $table->unique(['load_id', 'transporter_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('load_bids');
    }
};
