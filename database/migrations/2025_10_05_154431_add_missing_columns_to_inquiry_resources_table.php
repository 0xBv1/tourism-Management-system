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
        Schema::table('inquiry_resources', function (Blueprint $table) {
            // Add resource_name column for internal services
            $table->string('resource_name')->nullable()->after('resource_id');
            
            // Make resource_id nullable for internal services
            $table->unsignedBigInteger('resource_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiry_resources', function (Blueprint $table) {
            // Drop resource_name column
            $table->dropColumn('resource_name');
            
            // Make resource_id not nullable again
            $table->unsignedBigInteger('resource_id')->nullable(false)->change();
        });
    }
};
