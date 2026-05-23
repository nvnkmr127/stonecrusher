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
            $table->foreignId('crusher_expense_id')->nullable()->constrained('crusher_expenses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operational_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('crusher_expense_id');
        });
    }
};
