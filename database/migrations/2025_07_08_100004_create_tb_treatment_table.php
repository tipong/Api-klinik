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
        Schema::create('tb_treatment', function (Blueprint $table) {
            $table->increments('id_treatment');
            $table->unsignedInteger('id_jenis_treatment');
            $table->string('nama_treatment', 255);
            $table->text('deskripsi_treatment');
            $table->decimal('biaya_treatment', 15, 2);
            $table->time('estimasi_treatment');
            $table->string('gambar_treatment', 255);
            $table->timestamps();
            
            $table->foreign('id_jenis_treatment')->references('id_jenis_treatment')->on('tb_jenis_treatment')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_treatment');
    }
};
