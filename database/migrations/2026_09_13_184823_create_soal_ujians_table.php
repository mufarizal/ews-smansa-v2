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
        Schema::create('soal_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_harian_id')->constrained('ujian_harians')->cascadeOnDelete();
            $table->enum('tipe_soal', ['pilihan_ganda', 'esai']);
            $table->text('pertanyaan');
            $table->string('opsi_a')->nullable();
            $table->string('opsi_b')->nullable();
            $table->string('opsi_c')->nullable();
            $table->string('opsi_d')->nullable();
            $table->string('kunci_jawaban', 50)->nullable();
            $table->unsignedInteger('poin');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_ujians');
    }
};
