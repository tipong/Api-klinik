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
        Schema::create('tb_kompensasi', function (Blueprint $table) {
            $table->increments('id_kompensasi');
            $table->unsignedInteger('id_treatment');
            $table->string('nama_kompensasi', 255);
            $table->text('deskripsi_kompensasi')->nullable();
            $table->timestamps();
            
            $table->foreign('id_treatment')->references('id_treatment')->on('tb_treatment')->onDelete('cascade');
        });
        
        Schema::create('tb_komplain', function (Blueprint $table) {
            $table->increments('id_komplain');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_booking_treatment');
            $table->unsignedInteger('id_detail_booking_treatment');
            $table->text('teks_komplain')->nullable();
            $table->text('gambar_komplain')->nullable();
            $table->text('balasan_komplain')->nullable();
            $table->enum('pemberian_kompensasi', ['Tidak ada pemberian', 'Sudah diberikan'])->default('Tidak ada pemberian');
            $table->timestamps();
            
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
            $table->foreign('id_booking_treatment')->references('id_booking_treatment')->on('tb_booking_treatment')->onDelete('cascade');
        });
        
        Schema::create('tb_kompensasi_diberikan', function (Blueprint $table) {
            $table->increments('id_kompensasi_diberikan');
            $table->unsignedInteger('id_komplain')->nullable();
            $table->unsignedInteger('id_kompensasi')->nullable();
            $table->string('kode_kompensasi', 255)->nullable()->unique();
            $table->date('tanggal_berakhir_kompensasi')->nullable();
            $table->enum('status_kompensasi', ['Belum Digunakan', 'Sudah Digunakan', 'Sudah Kadaluwarsa'])->default('Belum Digunakan');
            $table->dateTime('tanggal_pemakaian_kompensasi')->nullable();
            $table->timestamps();
            
            $table->foreign('id_komplain')->references('id_komplain')->on('tb_komplain')->onDelete('cascade');
            $table->foreign('id_kompensasi')->references('id_kompensasi')->on('tb_kompensasi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_kompensasi_diberikan');
        Schema::dropIfExists('tb_komplain');
        Schema::dropIfExists('tb_kompensasi');
    }
};
