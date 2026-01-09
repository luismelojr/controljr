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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->bigInteger('amount_cents');
            $table->string('status'); // pending, confirmed, received, overdue, refunded
            $table->string('payment_method'); // pix, boleto, credit_card
            $table->string('payment_gateway')->default('asaas');
            $table->string('external_payment_id')->nullable()->index();
            $table->string('invoice_url')->nullable();
            $table->text('pix_qr_code')->nullable();
            $table->text('pix_copy_paste')->nullable();
            $table->string('boleto_barcode')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('subscription_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
