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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained();
            $table->timestamp('started_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('status')->default('active'); // active, cancelled, expired, pending
            $table->string('payment_gateway')->nullable(); // asaas
            $table->string('external_subscription_id')->nullable(); // ID da assinatura no Asaas
            $table->string('external_customer_id')->nullable(); // ID do cliente no Asaas
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('external_subscription_id');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
