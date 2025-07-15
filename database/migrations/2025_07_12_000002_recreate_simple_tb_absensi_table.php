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
        // Drop existing table and recreate with new simplified structure
        Schema::dropIfExists('tb_absensi');
        
        Schema::create('tb_absensi', function (Blueprint $table) {
            $table->increments('id_absensi');
            $table->unsignedInteger('id_pegawai');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpa'])->default('Hadir');
            $table->timestamps();
            
            $table->foreign('id_pegawai')->references('id_pegawai')->on('tb_pegawai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_absensi');
    }
};
