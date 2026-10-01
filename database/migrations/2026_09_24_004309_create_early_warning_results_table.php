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
        Schema::create('early_warning_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_hitung');
            $table->timestamp('generated_at');
            $table->decimal('c1_akademik', 6, 2);
            $table->decimal('c2_absensi', 6, 2);
            $table->decimal('c3_perilaku', 6, 2);
            $table->decimal('total_perilaku_negatif', 6, 2)->default(0);
            $table->decimal('total_perilaku_positif', 6, 2)->default(0);
            $table->decimal('r1_absensi', 6, 4);
            $table->decimal('r2_perilaku', 6, 4);
            $table->decimal('r3_akademik', 6, 4);
            $table->decimal('skor_akhir', 6, 4);
            $table->enum('kategori', ['aman', 'perhatian', 'binaan']);
            $table->boolean('data_tidak_lengkap')->default(false);
            $table->json('input_metadata')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'kelas_id', 'tanggal_hitung']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('early_warning_results');
    }
};
