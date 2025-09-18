<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->string('license_plate');
            $table->integer('capacity');
            $table->text('description')->nullable();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('driver_license')->nullable();
            $table->decimal('price_per_hour', 10, 2)->nullable();
            $table->decimal('price_per_day', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('fuel_type')->nullable();
            $table->string('transmission')->nullable();
            $table->text('features')->nullable();
            $table->text('images')->nullable();
            $table->string('status')->default('available');
            $table->boolean('active')->default(true);
            $table->boolean('enabled')->default(true);
            $table->date('insurance_expiry')->nullable();
            $table->date('registration_expiry')->nullable();
            $table->date('last_maintenance')->nullable();
            $table->date('next_maintenance')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city_id', 'status']);
            $table->index(['active', 'enabled']);
            $table->index('type');
            $table->index('capacity');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
};




