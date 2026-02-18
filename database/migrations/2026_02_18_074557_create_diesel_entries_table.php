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
        Schema::create('diesel_entries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->decimal('liters', 10, 2);
            $table->string('purpose');
            $table->string('driver_name');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diesel_entries');
    }
};
