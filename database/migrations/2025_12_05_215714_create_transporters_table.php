<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transporters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('registration_number')->unique();
            $table->string('license_number')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->text('address')->nullable();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->json('service_areas')->nullable(); // countries/regions they serve
            $table->json('vehicle_types')->nullable(); // truck, van, cargo plane, etc
            $table->integer('fleet_size')->default(1);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_deliveries')->default(0);
            $table->integer('successful_deliveries')->default(0);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('documents')->nullable(); // insurance, licenses
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index(['country_id', 'status']);
            $table->index('registration_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transporters');
    }
};
