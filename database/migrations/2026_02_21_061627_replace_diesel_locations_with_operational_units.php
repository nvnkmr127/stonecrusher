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
        Schema::rename('diesel_locations', 'operational_units');

        Schema::table('operational_units', function (Blueprint $table) {
            $table->string('code', 10)->nullable()->after('id')->unique();
        });

        // Drop the constraint if it exists, otherwise ignore. SQLite handles renames differently but Laravel handles it.
        Schema::table('vehicles', function (Blueprint $table) {
            $table->renameColumn('diesel_location_id', 'operational_unit_id');
        });

        Schema::table('diesel_entries', function (Blueprint $table) {
            $table->renameColumn('diesel_location_id', 'operational_unit_id');
        });

        \Illuminate\Support\Facades\DB::table('operational_units')->truncate();
        \Illuminate\Support\Facades\DB::table('operational_units')->insert([
            ['code' => 'QRY', 'name' => 'Quarry', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CRS', 'name' => 'Crusher', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'EXT', 'name' => 'External Delivery', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Nullify foreign keys to old missing IDs securely
        \Illuminate\Support\Facades\DB::table('vehicles')->update(['operational_unit_id' => null]);
        \Illuminate\Support\Facades\DB::table('diesel_entries')->update(['operational_unit_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diesel_entries', function (Blueprint $table) {
            $table->renameColumn('operational_unit_id', 'diesel_location_id');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->renameColumn('operational_unit_id', 'diesel_location_id');
        });

        Schema::table('operational_units', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::rename('operational_units', 'diesel_locations');
    }
};
