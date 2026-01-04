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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name'); // Free, Premium, Family
            $table->string('slug')->unique(); // free, premium, family
            $table->bigInteger('price_cents')->default(0); // 0, 1990, 2990
            $table->string('billing_period')->default('monthly'); // monthly, yearly
            $table->json('features')->nullable(); // Limites e features do plano
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('max_users')->default(1); // 1 para Free/Premium, 5 para Family
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
