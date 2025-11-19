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
        Schema::dropIfExists('saved_reports');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible - the original migration has been deleted
    }
};
