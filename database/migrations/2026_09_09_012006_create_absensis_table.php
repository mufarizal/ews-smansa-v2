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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('jadwal_id')->constrained('jadwals')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('tipe', ['mapel'])->default('mapel');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha', 'terlambat']);
            $table->unsignedInteger('menit_terlambat')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'jadwal_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
