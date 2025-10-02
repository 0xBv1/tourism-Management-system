<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('address');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('cuisine_type');
            $table->string('price_range')->nullable();
            $table->decimal('price_per_meal', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->text('cuisines')->nullable();
            $table->text('features')->nullable();
            $table->text('amenities')->nullable();
            $table->text('images')->nullable();
            $table->string('status')->default('available');
            $table->boolean('active')->default(true);
            $table->boolean('enabled')->default(true);
            $table->text('opening_hours')->nullable();
            $table->integer('capacity')->nullable();
            $table->boolean('reservation_required')->default(false);
            $table->string('dress_code')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city_id', 'status']);
            $table->index(['active', 'enabled']);
            $table->index('cuisine_type');
            $table->index('price_range');
            $table->index('capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
