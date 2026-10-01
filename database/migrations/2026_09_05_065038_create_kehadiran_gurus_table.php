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
        Schema::create('kehadiran_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('sesi', ['pagi', 'sore']);
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa']);
            $table->text('keterangan')->nullable();
            $table->time('waktu_absen')->nullable();
            $table->timestamps();

            $table->unique(['guru_id', 'tanggal', 'sesi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadiran_gurus');
    }
};
