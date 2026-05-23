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
        // 1. contractor_advances
        Schema::create('contractor_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->date('date');
            $table->decimal('amount', 12, 2);
            $table->string('payment_mode')->default('cash'); // 'cash', 'bank', 'upi'
            $table->string('reference_number')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['vendor_id', 'date']);
        });

        // 2. quarry_drilling_logs
        Schema::create('quarry_drilling_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_unit_id')->constrained('operational_units')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade'); // contractor rig
            $table->date('date');
            $table->integer('no_of_holes')->default(0);
            $table->decimal('total_feet', 10, 2)->default(0.00);
            $table->decimal('rate_per_foot', 10, 2)->default(0.00);
            $table->decimal('gross_amount', 12, 2); // (total_feet * rate_per_foot)
            
            // Deductions & Adjustments
            $table->decimal('diesel_deduction_amount', 12, 2)->default(0.00);
            $table->decimal('advance_deduction_amount', 12, 2)->default(0.00);
            $table->decimal('net_amount', 12, 2); // gross - diesel - advance
            
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['operational_unit_id', 'date']);
            $table->index(['vendor_id', 'date']);
        });

        // 3. quarry_blasts
        Schema::create('quarry_blasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_unit_id')->constrained('operational_units')->onDelete('cascade');
            $table->date('date');
            $table->string('blast_number')->unique();
            $table->integer('holes_blasted')->default(0);
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['operational_unit_id', 'date']);
        });

        // 4. quarry_blasting_materials_used
        Schema::create('quarry_blasting_materials_used', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quarry_blast_id')->constrained('quarry_blasts')->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null'); // supplier
            $table->string('material_type'); // 'explosives_kg', 'detonators_ordinary', 'detonators_electric', 'wiring_m'
            $table->decimal('quantity', 12, 2);
            $table->decimal('rate', 10, 2);
            $table->decimal('amount', 12, 2); // quantity * rate
            $table->timestamps();
        });

        // 5. quarry_secondary_blastings
        Schema::create('quarry_secondary_blastings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_unit_id')->constrained('operational_units')->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null'); // blasting contractor
            $table->date('date');
            $table->integer('no_of_holes')->default(0);
            $table->decimal('amount', 12, 2);
            $table->decimal('diesel_deduction_amount', 12, 2)->default(0.00);
            $table->decimal('net_amount', 12, 2); // amount - diesel
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['operational_unit_id', 'date']);
        });

        // 6. quarry_labour_sheets
        Schema::create('quarry_labour_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_unit_id')->constrained('operational_units')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade'); // labour contractor
            $table->date('date');
            $table->integer('no_of_workers')->default(0);
            $table->decimal('rate_per_worker', 10, 2)->default(0.00);
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('advance_deduction_amount', 12, 2)->default(0.00);
            $table->decimal('net_amount', 12, 2); // gross - advance
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['operational_unit_id', 'date']);
            $table->index(['vendor_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarry_labour_sheets');
        Schema::dropIfExists('quarry_secondary_blastings');
        Schema::dropIfExists('quarry_blasting_materials_used');
        Schema::dropIfExists('quarry_blasts');
        Schema::dropIfExists('quarry_drilling_logs');
        Schema::dropIfExists('contractor_advances');
    }
};
