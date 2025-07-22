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
        Schema::table('tb_hasil_seleksi', function (Blueprint $table) {
            // Hapus foreign key constraint lama
            $table->dropForeign(['id_lowongan_pekerjaan']);
            
            // Hapus kolom id_lowongan_pekerjaan
            $table->dropColumn('id_lowongan_pekerjaan');
            
            // Tambah kolom id_lamaran_pekerjaan
            $table->unsignedInteger('id_lamaran_pekerjaan')->after('id_user');
            
            // Tambah foreign key constraint baru
            $table->foreign('id_lamaran_pekerjaan')->references('id_lamaran_pekerjaan')->on('tb_lamaran_pekerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_hasil_seleksi', function (Blueprint $table) {
            // Hapus foreign key constraint baru
            $table->dropForeign(['id_lamaran_pekerjaan']);
            
            // Hapus kolom id_lamaran_pekerjaan
            $table->dropColumn('id_lamaran_pekerjaan');
            
            // Tambah kembali kolom id_lowongan_pekerjaan
            $table->unsignedInteger('id_lowongan_pekerjaan')->after('id_user');
            
            // Tambah kembali foreign key constraint lama
            $table->foreign('id_lowongan_pekerjaan')->references('id_lowongan_pekerjaan')->on('tb_lowongan_pekerjaan');
        });
    }
};
