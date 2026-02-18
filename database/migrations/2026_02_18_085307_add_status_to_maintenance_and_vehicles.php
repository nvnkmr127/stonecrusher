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
        Schema::table('vehicle_maintenances', function (Blueprint $table) {
            $table->string('status')->default('Completed')->after('type'); // Pending, In Progress, Completed
            $table->date('completion_date')->nullable()->after('date');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('operational_status')->default('Operational')->after('is_active'); // Operational, Under Maintenance, Broken Down
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_maintenances', function (Blueprint $table) {
            $table->dropColumn(['status', 'completion_date']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('operational_status');
        });
    }
};
