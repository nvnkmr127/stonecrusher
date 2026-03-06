<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gate_passes', function (Blueprint $table) {
            if (!Schema::hasColumn('gate_passes', 'lead')) {
                $table->decimal('lead', 10, 2)->default(0)->after('diesel_amount');
            }
        });

        // Data migration: lead = transport_cost + diesel_amount
        DB::table('gate_passes')->update([
            'lead' => DB::raw('COALESCE(transport_cost, 0) + COALESCE(diesel_amount, 0)')
        ]);

        // Drop the old columns as requested: "Remove both original fields"
        Schema::table('gate_passes', function (Blueprint $table) {
            $table->dropColumn(['transport_cost', 'diesel_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gate_passes', function (Blueprint $table) {
            $table->decimal('transport_cost', 10, 2)->nullable()->after('lead');
            $table->decimal('diesel_amount', 10, 2)->nullable()->after('transport_cost');
        });

        // Re-transfer back (simplistic: put everything in transport_cost?)
        DB::table('gate_passes')->update([
            'transport_cost' => DB::raw('lead')
        ]);

        Schema::table('gate_passes', function (Blueprint $table) {
            $table->dropColumn('lead');
        });
    }
};
