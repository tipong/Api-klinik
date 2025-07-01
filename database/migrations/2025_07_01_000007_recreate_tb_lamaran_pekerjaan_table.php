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
        // Drop existing table if it exists to recreate with correct structure
        Schema::dropIfExists('tb_lamaran_pekerjaan');
        
        Schema::create('tb_lamaran_pekerjaan', function (Blueprint $table) {
            $table->increments('id_lamaran_pekerjaan');
            $table->unsignedInteger('id_lowongan_pekerjaan');
            $table->unsignedInteger('id_user');
            $table->string('nama_pelamar', 100);
            $table->string('email_pelamar', 100);
            $table->string('NIK_pelamar', 16)->nullable();
            $table->string('telepon_pelamar', 20);
            $table->text('alamat_pelamar');
            $table->string('pendidikan_terakhir', 50);
            $table->binary('CV')->nullable();
            $table->enum('status_lamaran', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->string('status_seleksi', 50)->nullable();
            $table->timestamps();
            
            $table->foreign('id_lowongan_pekerjaan')->references('id_lowongan_pekerjaan')->on('tb_lowongan_pekerjaan');
            $table->foreign('id_user')->references('id_user')->on('tb_user');
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
