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
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->integer('month');
            $table->integer('year');
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users');
            $table->boolean('is_released')->default(false);
            $table->timestamp('released_at')->nullable();
            $table->foreignId('released_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
