<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gate_passes', function (Blueprint $table) {
            $table->index('date');
            $table->index('status');
        });

        Schema::table('client_transactions', function (Blueprint $table) {
            $table->index('transaction_date');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });

        Schema::table('client_transactions', function (Blueprint $table) {
            $table->dropIndex(['transaction_date']);
        });

        Schema::table('gate_passes', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['date']);
        });
    }
};
