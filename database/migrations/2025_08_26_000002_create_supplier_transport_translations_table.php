<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('supplier_transport_translations')) {
            Schema::create('supplier_transport_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('supplier_transport_id');
                $table->string('locale', 10)->index();
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();

                $table->unique(['supplier_transport_id', 'locale'], 'supplier_transport_locale_unique');
                $table->foreign('supplier_transport_id')
                    ->references('id')->on('supplier_transports')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('supplier_transport_translations')) {
            Schema::dropIfExists('supplier_transport_translations');
        }
    }
};


