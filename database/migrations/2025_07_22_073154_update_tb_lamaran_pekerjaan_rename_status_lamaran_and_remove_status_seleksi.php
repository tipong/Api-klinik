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
        Schema::table('tb_lamaran_pekerjaan', function (Blueprint $table) {
            // Rename kolom status_lamaran menjadi status
            $table->renameColumn('status_lamaran', 'status');
            
            // Hapus kolom status_seleksi
            $table->dropColumn('status_seleksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_lamaran_pekerjaan', function (Blueprint $table) {
            // Rename kembali kolom status menjadi status_lamaran
            $table->renameColumn('status', 'status_lamaran');
            
            // Tambah kembali kolom status_seleksi
            $table->string('status_seleksi', 50)->nullable();
        });
    }
};
