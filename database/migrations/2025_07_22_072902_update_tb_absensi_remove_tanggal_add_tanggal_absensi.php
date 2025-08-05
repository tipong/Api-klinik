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
        Schema::table('tb_absensi', function (Blueprint $table) {
            // Check if column exists before dropping (for MySQL safety)
            if (Schema::hasColumn('tb_absensi', 'tanggal')) {
                $table->dropColumn('tanggal');
            }
            
            // Add new column if not exists
            if (!Schema::hasColumn('tb_absensi', 'tanggal_absensi')) {
                $table->date('tanggal_absensi')->after('id_pegawai')->default(now()->toDateString());
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_absensi', function (Blueprint $table) {
            // Kembalikan kolom tanggal
            $table->date('tanggal')->nullable()->after('id_pegawai');
            
            // Hapus kolom tanggal_absensi
            $table->dropColumn('tanggal_absensi');
        });
    }
};
