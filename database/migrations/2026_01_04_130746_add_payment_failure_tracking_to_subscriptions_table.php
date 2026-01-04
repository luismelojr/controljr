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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedInteger('failed_payments_count')->default(0)->after('status');
            $table->timestamp('last_payment_failed_at')->nullable()->after('failed_payments_count');
            $table->timestamp('payment_grace_period_ends_at')->nullable()->after('last_payment_failed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['failed_payments_count', 'last_payment_failed_at', 'payment_grace_period_ends_at']);
        });
    }
};
