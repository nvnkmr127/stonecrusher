<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->decimal('total_payable', 15, 2)->default(0)->after('year');
            $table->decimal('total_paid', 15, 2)->default(0)->after('total_payable');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->dropColumn(['total_payable', 'total_paid']);
        });
    }
};
