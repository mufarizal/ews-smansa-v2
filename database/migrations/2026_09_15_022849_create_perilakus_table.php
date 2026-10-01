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
        Schema::create('perilakus', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis', ['positif', 'negatif']);
            $table->unsignedInteger('poin');
            $table->text('keterangan')->nullable();
            $table->boolean('is_default_aman')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perilakus');
    }
};
