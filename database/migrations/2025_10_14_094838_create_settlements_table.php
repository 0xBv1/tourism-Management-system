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
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->string('settlement_number')->unique(); // Unique settlement number
            $table->string('settlement_type'); // monthly, weekly, quarterly, yearly, custom
            $table->string('resource_type'); // guide, representative, hotel, vehicle, dahabia, restaurant, ticket, extra
            $table->unsignedBigInteger('resource_id');
            $table->integer('month')->nullable(); // For monthly settlements
            $table->integer('year');
            $table->date('start_date')->nullable(); // For custom settlements
            $table->date('end_date')->nullable(); // For custom settlements
            $table->string('commission_type')->default('percentage'); // percentage, fixed, none
            $table->decimal('commission_value', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('bonuses', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->integer('total_bookings')->default(0);
            $table->integer('total_hours')->default(0);
            $table->integer('total_days')->default(0);
            $table->string('status')->default('pending'); // pending, calculated, approved, rejected, paid
            $table->string('currency')->default('USD');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('calculated_by')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['resource_type', 'resource_id']);
            $table->index(['settlement_type', 'year', 'month']);
            $table->index('status');
            $table->foreign('calculated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
