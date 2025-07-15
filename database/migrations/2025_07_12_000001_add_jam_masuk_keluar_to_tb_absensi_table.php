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
            $table->time('jam_masuk')->nullable()->after('tanggal');
            $table->time('jam_keluar')->nullable()->after('jam_masuk');
            $table->string('lokasi_masuk', 500)->nullable()->after('jam_keluar');
            $table->string('lokasi_keluar', 500)->nullable()->after('lokasi_masuk');
            $table->text('keterangan')->nullable()->after('lokasi_keluar');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpa'])->default('Hadir')->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_absensi', function (Blueprint $table) {
            $table->dropColumn([
                'jam_masuk',
                'jam_keluar', 
                'lokasi_masuk',
                'lokasi_keluar',
                'keterangan',
                'status'
            ]);
        });
    }
};
