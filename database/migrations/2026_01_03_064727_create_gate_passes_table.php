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
        Schema::create('gate_passes', function (Blueprint $table) {
            $table->id();
            $table->string('gate_pass_number')->unique();
            $table->dateTime('date');

            // Relationships
            $table->foreignId('vehicle_id')->constrained()->onDelete('restrict');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('metal_type_id')->constrained()->onDelete('restrict');

            // Details
            $table->string('driver_name');
            $table->decimal('gross_weight', 10, 2)->default(0);
            $table->decimal('tare_weight', 10, 2)->default(0);
            $table->decimal('net_weight', 10, 2)->default(0);

            // Financials
            $table->decimal('rate_per_ton', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('diesel_amount', 10, 2)->default(0);
            $table->decimal('advance_amount', 10, 2)->default(0);

            // Status
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending');
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_passes');
    }
};
