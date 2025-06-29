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
        Schema::create('recruitments', function (Blueprint $table) {
            $table->id();
            $table->string('judul_lowongan'); // Judul lowongan kerja
            $table->string('posisi'); // Posisi yang dibutuhkan
            $table->text('deskripsi_pekerjaan'); // Deskripsi pekerjaan
            $table->text('persyaratan'); // Persyaratan
            $table->text('benefit')->nullable(); // Benefit yang ditawarkan
            $table->decimal('gaji_min', 15, 2)->nullable(); // Gaji minimum
            $table->decimal('gaji_max', 15, 2)->nullable(); // Gaji maksimum
            $table->string('lokasi_kerja'); // Lokasi kerja
            $table->enum('tipe_kerja', ['full_time', 'part_time', 'kontrak', 'magang'])->default('full_time');
            $table->integer('jumlah_posisi')->default(1); // Jumlah posisi yang dibutuhkan
            $table->date('tanggal_posting'); // Tanggal posting lowongan
            $table->date('batas_lamaran'); // Batas akhir lamaran
            $table->enum('status', ['aktif', 'nonaktif', 'draft'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Yang membuat lowongan
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'tanggal_posting']);
            $table->index(['posisi', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitments');
    }
};
