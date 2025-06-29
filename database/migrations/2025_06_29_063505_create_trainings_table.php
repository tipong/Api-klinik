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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('judul_pelatihan'); // Judul pelatihan
            $table->text('deskripsi'); // Deskripsi pelatihan
            $table->text('tujuan_pelatihan'); // Tujuan pelatihan
            $table->text('materi_pelatihan'); // Materi yang akan diajarkan
            $table->string('instruktur'); // Nama instruktur
            $table->string('lokasi'); // Lokasi pelatihan
            $table->datetime('tanggal_mulai'); // Tanggal dan jam mulai
            $table->datetime('tanggal_selesai'); // Tanggal dan jam selesai
            $table->integer('durasi_jam'); // Durasi dalam jam
            $table->integer('kapasitas_peserta'); // Kapasitas maksimal peserta
            $table->integer('peserta_terdaftar')->default(0); // Jumlah peserta yang sudah daftar
            $table->enum('kategori', ['teknis', 'soft_skill', 'keselamatan', 'customer_service', 'manajemen'])->default('teknis');
            $table->enum('level', ['basic', 'intermediate', 'advanced'])->default('basic');
            $table->enum('tipe', ['online', 'offline', 'hybrid'])->default('offline');
            $table->decimal('biaya', 15, 2)->default(0); // Biaya pelatihan
            $table->enum('status', ['draft', 'open', 'running', 'completed', 'cancelled'])->default('draft');
            $table->text('persyaratan')->nullable(); // Persyaratan untuk mengikuti
            $table->json('materi_file')->nullable(); // File materi pelatihan
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Yang membuat pelatihan
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'tanggal_mulai']);
            $table->index(['kategori', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
