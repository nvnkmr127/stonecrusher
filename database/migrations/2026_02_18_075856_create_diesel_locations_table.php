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
        Schema::create('diesel_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Update diesel_entries to use foreign key
        Schema::table('diesel_entries', function (Blueprint $table) {
            $table->foreignId('diesel_location_id')->nullable()->after('vehicle_id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diesel_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('diesel_location_id');
        });
        Schema::dropIfExists('diesel_locations');
    }
};
