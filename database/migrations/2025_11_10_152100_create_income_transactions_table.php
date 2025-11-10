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
        Schema::create('income_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('income_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('month_reference'); // YYYY-MM format
            $table->integer('amount'); // stored in cents
            $table->date('expected_date');
            $table->date('received_at')->nullable();
            $table->integer('installment_number')->nullable(); // qual parcela é (1/10, 2/10...)
            $table->integer('total_installments')->nullable(); // total de parcelas
            $table->boolean('is_received')->default(false);
            $table->enum('status', ['pending', 'received', 'overdue'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_transactions');
    }
};
