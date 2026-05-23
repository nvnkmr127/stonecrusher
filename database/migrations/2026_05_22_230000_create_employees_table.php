<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Create employees table
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->default('office'); // driver, operational, office, etc.
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('daily_rate', 10, 2)->default(0);
            $table->foreignId('operational_unit_id')->nullable()->constrained('operational_units')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Migrate data from users to employees, and create mapping
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $role = $user->department ?: 'office';
            
            DB::table('employees')->insert([
                'name' => $user->name,
                'role' => $role,
                'base_salary' => $user->base_salary ?? 0,
                'daily_rate' => $user->daily_rate ?? 0,
                'operational_unit_id' => null,
                'user_id' => $user->id,
                'is_active' => $user->is_active ?? true,
                'created_at' => $user->created_at ?? now(),
                'updated_at' => $user->updated_at ?? now(),
            ]);
        }

        // 3. Create the new attendances table and copy mapped data
        Schema::create('attendances_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('status')->default('present');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'date']);
        });

        $attendances = DB::table('attendances')->get();
        foreach ($attendances as $att) {
            $employee = DB::table('employees')->where('user_id', $att->user_id)->first();
            if ($employee) {
                DB::table('attendances_new')->insert([
                    'id' => $att->id,
                    'employee_id' => $employee->id,
                    'date' => $att->date,
                    'check_in' => $att->check_in,
                    'check_out' => $att->check_out,
                    'status' => $att->status,
                    'remarks' => $att->remarks,
                    'created_at' => $att->created_at,
                    'updated_at' => $att->updated_at,
                ]);
            }
        }

        // 4. Create the new salary_advances table and copy mapped data
        Schema::create('salary_advances_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_mode')->nullable();
            $table->date('date');
            $table->string('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $advances = DB::table('salary_advances')->get();
        foreach ($advances as $adv) {
            $employee = DB::table('employees')->where('user_id', $adv->user_id)->first();
            if ($employee) {
                DB::table('salary_advances_new')->insert([
                    'id' => $adv->id,
                    'employee_id' => $employee->id,
                    'amount' => $adv->amount,
                    'payment_mode' => $adv->payment_mode,
                    'date' => $adv->date,
                    'remarks' => $adv->remarks,
                    'created_at' => $adv->created_at,
                    'updated_at' => $adv->updated_at,
                    'deleted_at' => $adv->deleted_at ?? null,
                ]);
            }
        }

        // 5. Swap tables
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('salary_advances');
        Schema::rename('attendances_new', 'attendances');
        Schema::rename('salary_advances_new', 'salary_advances');

        // 6. Add driver_id to diesel_entries and gate_passes
        Schema::table('diesel_entries', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->constrained('employees')->onDelete('set null');
        });

        Schema::table('gate_passes', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->constrained('employees')->onDelete('set null');
        });

        // 7. Clean up users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['base_salary', 'daily_rate']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Re-add columns to users
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('daily_rate', 10, 2)->default(0);
        });

        // 2. Restore user_id data from employees back to users table
        $employees = DB::table('employees')->get();
        foreach ($employees as $employee) {
            if ($employee->user_id) {
                DB::table('users')->where('id', $employee->user_id)->update([
                    'base_salary' => $employee->base_salary,
                    'daily_rate' => $employee->daily_rate,
                ]);
            }
        }

        // 3. Recreate original attendances table
        Schema::create('attendances_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('status')->default('present');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'date']);
        });

        $attendances = DB::table('attendances')->get();
        foreach ($attendances as $att) {
            $employee = DB::table('employees')->where('id', $att->employee_id)->first();
            if ($employee && $employee->user_id) {
                DB::table('attendances_old')->insert([
                    'id' => $att->id,
                    'user_id' => $employee->user_id,
                    'date' => $att->date,
                    'check_in' => $att->check_in,
                    'check_out' => $att->check_out,
                    'status' => $att->status,
                    'remarks' => $att->remarks,
                    'created_at' => $att->created_at,
                    'updated_at' => $att->updated_at,
                ]);
            }
        }

        // 4. Recreate original salary_advances table
        Schema::create('salary_advances_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_mode')->nullable();
            $table->date('date');
            $table->string('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $advances = DB::table('salary_advances')->get();
        foreach ($advances as $adv) {
            $employee = DB::table('employees')->where('id', $adv->employee_id)->first();
            if ($employee && $employee->user_id) {
                DB::table('salary_advances_old')->insert([
                    'id' => $adv->id,
                    'user_id' => $employee->user_id,
                    'amount' => $adv->amount,
                    'payment_mode' => $adv->payment_mode,
                    'date' => $adv->date,
                    'remarks' => $adv->remarks,
                    'created_at' => $adv->created_at,
                    'updated_at' => $adv->updated_at,
                    'deleted_at' => $adv->deleted_at ?? null,
                ]);
            }
        }

        // 5. Swap tables back
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('salary_advances');
        Schema::rename('attendances_old', 'attendances');
        Schema::rename('salary_advances_old', 'salary_advances');

        // 6. Drop driver_id from diesel_entries and gate_passes
        Schema::table('gate_passes', function (Blueprint $table) {
            $table->dropColumn('driver_id');
        });
        Schema::table('diesel_entries', function (Blueprint $table) {
            $table->dropColumn('driver_id');
        });

        // 7. Drop employees table
        Schema::dropIfExists('employees');

        Schema::enableForeignKeyConstraints();
    }
};
