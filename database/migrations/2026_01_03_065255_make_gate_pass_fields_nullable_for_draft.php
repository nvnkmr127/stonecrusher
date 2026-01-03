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
            $table->unsignedBigInteger('metal_type_id')->nullable()->change();
            $table->string('driver_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gate_passes', function (Blueprint $table) {
            // Reversing this might fail if there are nulls, but for dev it's ok.
            // We usually don't strictly require reverting to non-nullable without cleaning data.
            // $table->unsignedBigInteger('metal_type_id')->nullable(false)->change();
            // $table->string('driver_name')->nullable(false)->change();
        });
    }
};
