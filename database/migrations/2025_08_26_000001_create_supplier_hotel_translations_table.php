<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('supplier_hotel_translations')) {
            Schema::create('supplier_hotel_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('supplier_hotel_id');
                $table->string('locale', 10)->index();
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->string('city')->nullable();
                $table->timestamps();

                $table->unique(['supplier_hotel_id', 'locale'], 'supplier_hotel_locale_unique');
                $table->foreign('supplier_hotel_id')
                    ->references('id')->on('supplier_hotels')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('supplier_hotel_translations')) {
            Schema::dropIfExists('supplier_hotel_translations');
        }
    }
};


