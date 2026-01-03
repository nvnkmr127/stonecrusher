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
        Schema::table('client_transactions', function (Blueprint $table) {
            $table->foreignId('gate_pass_id')->nullable()->after('client_id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_transactions', function (Blueprint $table) {
            $table->dropForeign(['gate_pass_id']);
            $table->dropColumn('gate_pass_id');
        });
    }
};
