<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penilaian_formatif', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa');
            $table->unsignedBigInteger('id_kategori_penilaian');
            $table->decimal('nilai_formatif', 5, 2)->nullable();
            $table->decimal('nilai_kehadiran', 5, 2)->nullable();
            $table->timestamps();

            // Indexes for faster lookup (FKs omitted due to unknown table names)
            $table->index('id_siswa');
            $table->index('id_kategori_penilaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_formatif');
    }
};

