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
        Schema::create('tb_absensi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pegawai_id');
            $table->date('tanggal');
            $table->timestamp('jam_masuk')->nullable();
            $table->timestamp('jam_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('lokasi_masuk', 255)->nullable();
            $table->string('lokasi_keluar', 255)->nullable();
            $table->string('status', 50)->default('Hadir');
            $table->timestamps();

            $table->foreign('pegawai_id')->references('id')->on('tb_pegawai');
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
