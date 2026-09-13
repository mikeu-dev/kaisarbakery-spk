<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hasil_topsises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->string('periode', 20)->default('2026-09')->index();
            $table->decimal('d_plus', 10, 6)->nullable();
            $table->decimal('d_minus', 10, 6)->nullable();
            $table->decimal('nilai_preferensi', 10, 4); // Vi
            $table->integer('ranking');
            $table->string('keterangan')->nullable(); // Kinerja Terbaik, Baik, Cukup, Kurang
            $table->timestamps();

            $table->unique(['karyawan_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_topsises');
    }
};
