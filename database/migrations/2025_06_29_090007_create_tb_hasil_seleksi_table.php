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
        Schema::create('tb_hasil_seleksi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lamaran_id');
            $table->date('tanggal_hasil');
            $table->enum('status', ['Diterima', 'Ditolak', 'Pending'])->default('Pending');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('lamaran_id')->references('id')->on('tb_lamaran_pekerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_hasil_seleksi');
    }
};
