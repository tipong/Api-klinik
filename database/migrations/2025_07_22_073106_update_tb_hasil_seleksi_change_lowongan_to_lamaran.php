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
        // Sudah menggunakan id_lamaran_pekerjaan dari migrasi awal untuk SQLite
        // Schema::table('tb_hasil_seleksi', function (Blueprint $table) {
        //     // Check if old column exists before dropping
        //     if (Schema::hasColumn('tb_hasil_seleksi', 'id_lowongan_pekerjaan')) {
        //         // Drop foreign key constraint first
        //         try {
        //             $table->dropForeign(['id_lowongan_pekerjaan']);
        //         } catch (\Exception $e) {
        //             // Continue if foreign key doesn't exist
        //         }
        //         $table->dropColumn('id_lowongan_pekerjaan');
        //     }
        //     
        //     // Add new column if not exists
        //     if (!Schema::hasColumn('tb_hasil_seleksi', 'id_lamaran_pekerjaan')) {
        //         $table->unsignedInteger('id_lamaran_pekerjaan')->after('id_user');
        //         $table->foreign('id_lamaran_pekerjaan')->references('id_lamaran_pekerjaan')->on('tb_lamaran_pekerjaan');
        //     }
        // });
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
