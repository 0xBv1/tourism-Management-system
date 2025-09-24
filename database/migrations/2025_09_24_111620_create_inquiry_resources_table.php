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
        Schema::create('inquiry_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained('inquiries')->onDelete('cascade');
            $table->string('resource_type'); // hotel, vehicle, guide, representative, extra
            $table->unsignedBigInteger('resource_id'); // FK to master data tables
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Unique constraint to prevent duplicate resources
            $table->unique(['inquiry_id', 'resource_type', 'resource_id'], 'inquiry_resource_unique');
            
            // Indexes for better performance
            $table->index(['resource_type', 'resource_id']);
            $table->index('added_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiry_resources');
    }
};
