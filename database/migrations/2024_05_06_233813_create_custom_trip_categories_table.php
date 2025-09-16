<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('custom_trip_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_trip_id')->constrained('custom_trips')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('customized_trip_categories')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_trip_categories');
    }
};
