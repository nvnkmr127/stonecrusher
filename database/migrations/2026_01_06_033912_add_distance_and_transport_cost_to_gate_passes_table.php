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
            $table->string('delivery_location')->nullable()->after('client_id');
            $table->decimal('distance_km', 10, 2)->nullable()->after('delivery_location');
            $table->decimal('transport_cost', 10, 2)->default(0)->after('distance_km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gate_passes', function (Blueprint $table) {
            $table->dropColumn(['delivery_location', 'distance_km', 'transport_cost']);
        });
    }
};
