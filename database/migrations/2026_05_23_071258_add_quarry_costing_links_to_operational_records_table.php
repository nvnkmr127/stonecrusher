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
            $table->foreignId('quarry_drilling_log_id')->nullable()->constrained('quarry_drilling_logs')->onDelete('cascade');
            $table->foreignId('quarry_blast_id')->nullable()->constrained('quarry_blasts')->onDelete('cascade');
            $table->foreignId('quarry_secondary_blasting_id')->nullable()->constrained('quarry_secondary_blastings')->onDelete('cascade');
            $table->foreignId('quarry_labour_sheet_id')->nullable()->constrained('quarry_labour_sheets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operational_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('quarry_drilling_log_id');
            $table->dropConstrainedForeignId('quarry_blast_id');
            $table->dropConstrainedForeignId('quarry_secondary_blasting_id');
            $table->dropConstrainedForeignId('quarry_labour_sheet_id');
        });
    }
};
