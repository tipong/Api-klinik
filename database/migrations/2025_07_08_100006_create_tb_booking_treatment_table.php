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
        Schema::create('tb_booking_treatment', function (Blueprint $table) {
            $table->increments('id_booking_treatment');
            $table->unsignedInteger('id_user');
            $table->dateTime('waktu_treatment');
            $table->unsignedInteger('id_dokter')->nullable();
            $table->unsignedInteger('id_beautician')->nullable();
            $table->enum('status_booking_treatment', ['Verifikasi', 'Berhasil dibooking', 'Dibatalkan', 'Selesai'])->default('Verifikasi');
            $table->decimal('harga_total', 15, 2)->nullable();
            $table->unsignedInteger('id_promo')->nullable();
            $table->decimal('potongan_harga', 15, 2)->nullable();
            $table->decimal('besaran_pajak', 15, 2)->default(0.00);
            $table->decimal('harga_akhir_treatment', 15, 2)->nullable();
            $table->timestamps();
            
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
            $table->foreign('id_dokter')->references('id_dokter')->on('tb_dokter')->onDelete('set null');
            $table->foreign('id_beautician')->references('id_beautician')->on('tb_beautician')->onDelete('set null');
            $table->foreign('id_promo')->references('id_promo')->on('tb_promo')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_booking_treatment');
    }
};
