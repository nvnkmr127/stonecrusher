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
        Schema::table('operational_records', function (Blueprint $table) {
            $table->foreignId('diesel_entry_id')->nullable()->constrained('diesel_entries')->onDelete('cascade');
            $table->foreignId('gate_pass_id')->nullable()->constrained('gate_passes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operational_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('diesel_entry_id');
            $table->dropConstrainedForeignId('gate_pass_id');
        });
    }
};
