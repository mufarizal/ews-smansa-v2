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
        Schema::create('jawaban_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_tugas_id')->constrained('soal_tugas')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->text('jawaban_text')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('poin_diperoleh')->nullable();
            $table->timestamps();

            $table->unique(['soal_tugas_id', 'siswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_tugas');
    }
};
