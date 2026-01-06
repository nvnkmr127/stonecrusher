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
        Schema::create('daily_closings', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_cash', 15, 2)->default(0);
            $table->decimal('total_expenses', 15, 2)->default(0);
            $table->enum('status', ['closed', 'open', 'reopened'])->default('closed');
            $table->foreignId('closed_by_user_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_closings');
    }
};
