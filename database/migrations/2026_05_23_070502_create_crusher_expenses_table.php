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
        Schema::create('crusher_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_unit_id')->constrained('operational_units')->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->date('date');
            $table->string('category'); // 'diesel', 'electricity', 'labour', 'maintenance', 'other'
            $table->decimal('amount', 12, 2);
            $table->decimal('quantity', 12, 2)->nullable(); // e.g. units of electricity, liters of diesel
            $table->decimal('rate', 10, 2)->nullable();     // unit rate
            $table->string('payment_mode')->default('cash'); // 'cash', 'bank', 'upi', 'on_account'
            $table->string('invoice_number', 50)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            // Performance indexes for aggregation & date-filtering
            $table->index(['operational_unit_id', 'date']);
            $table->index(['category', 'date']);
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crusher_expenses');
    }
};
