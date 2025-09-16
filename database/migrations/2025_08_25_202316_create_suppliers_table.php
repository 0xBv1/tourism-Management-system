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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name');
            $table->string('company_email')->unique();
            $table->string('phone');
            $table->text('address');
            $table->text('payment_info')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(10.00); // Default 10% commission
            $table->decimal('wallet_balance', 15, 2)->default(0.00);
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('business_license')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index('company_email');
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
