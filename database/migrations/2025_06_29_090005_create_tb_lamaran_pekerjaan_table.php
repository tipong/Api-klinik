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
        Schema::create('tb_lamaran_pekerjaan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lowongan_id');
            $table->unsignedInteger('user_id');
            $table->string('nama_pelamar', 100);
            $table->string('email_pelamar', 100);
            $table->string('no_telp_pelamar', 20)->nullable();
            $table->text('alamat_pelamar')->nullable();
            $table->string('pendidikan_terakhir', 50)->nullable();
            $table->string('pengalaman_kerja', 255)->nullable();
            $table->string('cv', 255)->nullable();
            $table->string('portofolio', 255)->nullable();
            $table->date('tanggal_lamaran');
            $table->enum('status', ['Diterima', 'Dalam Proses', 'Ditolak'])->default('Dalam Proses');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('lowongan_id')->references('id')->on('tb_lowongan_pekerjaan');
            $table->foreign('user_id')->references('id')->on('tb_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_lamaran_pekerjaan');
    }
};
