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
        Schema::create('tb_lowongan_pekerjaan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('posisi_id');
            $table->string('judul', 100);
            $table->text('deskripsi');
            $table->text('persyaratan')->nullable();
            $table->date('tanggal_posting');
            $table->date('tanggal_penutupan');
            $table->enum('status', ['Buka', 'Tutup'])->default('Buka');
            $table->integer('kuota')->default(1);
            $table->string('lokasi', 100)->nullable();
            $table->timestamps();

            $table->foreign('posisi_id')->references('id')->on('tb_posisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_lowongan_pekerjaan');
    }
};
