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
        Schema::create('diesel_stocks', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('opening_liters', 12, 2)->default(0);
            $table->decimal('purchased_liters', 12, 2)->default(0);
            $table->decimal('closing_liters', 12, 2)->default(0);
            $table->foreignId('operational_unit_id')->nullable()->constrained()->nullOnDelete();
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
        Schema::dropIfExists('diesel_stocks');
    }
};
