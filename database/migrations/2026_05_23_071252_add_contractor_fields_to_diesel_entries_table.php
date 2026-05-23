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
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null'); // contractor receiving fuel
            $table->boolean('is_deducted')->default(false); // whether this issue has been deducted from contractor bills
            $table->string('deducted_at_invoice_type')->nullable(); // 'drilling', 'secondary_blasting', etc.
            $table->unsignedBigInteger('deducted_at_invoice_id')->nullable();
            
            $table->index(['vendor_id', 'is_deducted']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diesel_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vendor_id');
            $table->dropColumn(['is_deducted', 'deducted_at_invoice_type', 'deducted_at_invoice_id']);
        });
    }
};
