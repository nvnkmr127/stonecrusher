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
        Schema::table('diesel_entries', function (Blueprint $table) {
            $table->renameColumn('purpose', 'work_type');
            $table->foreignId('gate_pass_id')->nullable()->after('operational_unit_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diesel_entries', function (Blueprint $table) {
            $table->renameColumn('work_type', 'purpose');
            $table->dropConstrainedForeignId('gate_pass_id');
        });
    }
};
