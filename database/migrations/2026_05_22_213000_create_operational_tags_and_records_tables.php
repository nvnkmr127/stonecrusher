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
        Schema::create('operational_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_unit_id')->constrained('operational_units')->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // 'expense' or 'revenue'
            $table->timestamps();

            $table->unique(['operational_unit_id', 'name']);
        });

        Schema::create('operational_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_unit_id')->constrained('operational_units')->onDelete('cascade');
            $table->foreignId('operational_tag_id')->constrained('operational_tags')->onDelete('cascade');
            $table->date('date');
            $table->decimal('quantity', 12, 2)->nullable();
            $table->decimal('rate', 12, 2)->nullable();
            $table->decimal('amount', 12, 2);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Seed initial tags for QRY (Quarry) and CRS (Crusher)
        $quarryId = DB::table('operational_units')->where('code', 'QRY')->value('id');
        $crusherId = DB::table('operational_units')->where('code', 'CRS')->value('id');

        if ($quarryId) {
            DB::table('operational_tags')->insert([
                ['operational_unit_id' => $quarryId, 'name' => 'Borewells', 'type' => 'expense', 'created_at' => now(), 'updated_at' => now()],
                ['operational_unit_id' => $quarryId, 'name' => 'Blasting Materials', 'type' => 'expense', 'created_at' => now(), 'updated_at' => now()],
                ['operational_unit_id' => $quarryId, 'name' => 'Diesel Used', 'type' => 'expense', 'created_at' => now(), 'updated_at' => now()],
                ['operational_unit_id' => $quarryId, 'name' => 'Secondary Blasting', 'type' => 'expense', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($crusherId) {
            DB::table('operational_tags')->insert([
                ['operational_unit_id' => $crusherId, 'name' => 'Metal Sale', 'type' => 'revenue', 'created_at' => now(), 'updated_at' => now()],
                ['operational_unit_id' => $crusherId, 'name' => 'Diesel Used', 'type' => 'expense', 'created_at' => now(), 'updated_at' => now()],
                ['operational_unit_id' => $crusherId, 'name' => 'Electricity', 'type' => 'expense', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_records');
        Schema::dropIfExists('operational_tags');
    }
};
