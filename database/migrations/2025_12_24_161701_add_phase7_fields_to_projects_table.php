<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('location')->nullable()->after('client_id');
            $table->decimal('estimated_quantity', 10, 2)->nullable()->after('description');
            $table->integer('progress')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['location', 'estimated_quantity', 'progress']);
        });
    }
};
