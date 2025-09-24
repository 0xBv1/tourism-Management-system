<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extras', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('category')->nullable(); // e.g., 'transportation', 'activities', 'services'
            $table->boolean('active')->default(true);
            $table->boolean('enabled')->default(true);
            $table->text('features')->nullable(); // Additional features or specifications (JSON as text for compatibility)
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['active', 'enabled']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extras');
    }
};
