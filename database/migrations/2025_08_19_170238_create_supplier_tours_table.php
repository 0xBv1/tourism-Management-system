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
        Schema::create('supplier_tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('highlights')->nullable();
            $table->text('included')->nullable();
            $table->text('excluded')->nullable();
            $table->string('duration');
            $table->string('type');
            $table->string('pickup_location')->nullable();
            $table->string('dropoff_location')->nullable();
            $table->decimal('adult_price', 10, 2);
            $table->decimal('child_price', 10, 2)->default(0);
            $table->decimal('infant_price', 10, 2)->default(0);
            $table->string('currency')->default('EGP');
            $table->integer('max_group_size')->default(20);
            $table->text('itinerary')->nullable();
            $table->text('images')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('enabled')->default(true);
            $table->boolean('approved')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['supplier_id', 'enabled']);
            $table->index(['type', 'enabled']);
            $table->index('approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_tours');
    }
};
