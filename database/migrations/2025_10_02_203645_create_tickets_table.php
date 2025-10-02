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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->decimal('price_per_person', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('duration_hours', 5, 2)->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('enabled')->default(true);
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->integer('max_participants')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city_id']);
            $table->index(['active', 'enabled']);
            $table->index('price_per_person');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
