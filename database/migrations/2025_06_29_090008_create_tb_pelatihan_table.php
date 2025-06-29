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
        Schema::create('tb_pelatihan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pegawai_id');
            $table->string('nama_pelatihan', 100);
            $table->string('jenis_pelatihan', 50);
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('penyelenggara', 100);
            $table->string('lokasi', 100);
            $table->decimal('biaya', 15, 2)->nullable();
            $table->enum('status', ['Terdaftar', 'Berjalan', 'Selesai', 'Dibatalkan'])->default('Terdaftar');
            $table->string('sertifikat', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('pegawai_id')->references('id')->on('tb_pegawai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pelatihan');
    }
};
