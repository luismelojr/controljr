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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // credit_card_usage, bill_due_date, account_balance, etc.
            $table->string('alertable_type')->nullable(); // Polymorphic: CreditCard, Account, Bill
            $table->unsignedBigInteger('alertable_id')->nullable();
            $table->decimal('trigger_value', 10, 2)->nullable(); // Ex: 80 para 80%
            $table->json('trigger_days')->nullable(); // Ex: [10, 3, 1] dias antes
            $table->json('notification_channels'); // ['mail', 'database']
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();

            // Index para relacionamento polimórfico
            $table->index(['alertable_type', 'alertable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
