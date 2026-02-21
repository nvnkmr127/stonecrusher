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
        Schema::table('gate_passes', function (Blueprint $table) {
            $table->foreignId('source_unit_id')->nullable()->constrained('operational_units')->onDelete('restrict');
            $table->foreignId('destination_unit_id')->nullable()->constrained('operational_units')->onDelete('restrict');
            $table->string('activity_type')->default('Sales')->after('metal_type_id');
            $table->integer('trips')->default(1)->after('net_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gate_passes', function (Blueprint $table) {
            $table->dropForeign(['source_unit_id']);
            $table->dropForeign(['destination_unit_id']);
            $table->dropColumn(['source_unit_id', 'destination_unit_id', 'activity_type', 'trips']);
        });
    }
};
