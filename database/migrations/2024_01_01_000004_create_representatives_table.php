<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('representatives', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('nationality')->nullable();
            $table->text('languages');
            $table->text('specializations')->nullable();
            $table->integer('experience_years');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->decimal('price_per_hour', 10, 2)->nullable();
            $table->decimal('price_per_day', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->text('bio')->nullable();
            $table->text('certifications')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('status')->default('available');
            $table->boolean('active')->default(true);
            $table->boolean('enabled')->default(true);
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->text('availability_schedule')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_license')->nullable();
            $table->text('service_areas')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city_id', 'status']);
            $table->index(['active', 'enabled']);
            $table->index('rating');
            $table->index('experience_years');
        });
    }

    public function down()
    {
        Schema::dropIfExists('representatives');
    }
};




