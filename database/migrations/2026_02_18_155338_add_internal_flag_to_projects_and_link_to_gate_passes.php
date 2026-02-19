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
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_internal')->default(false)->after('client_id');
            // Make client_id nullable for internal projects if needed
            $table->foreignId('client_id')->nullable()->change();
        });

        Schema::table('gate_passes', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained()->after('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gate_passes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('is_internal');
            $table->foreignId('client_id')->nullable(false)->change();
        });
    }
};
