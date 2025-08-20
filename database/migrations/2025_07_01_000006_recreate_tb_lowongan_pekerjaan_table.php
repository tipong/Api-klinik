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
        // Drop existing table if it exists to recreate with correct structure
        Schema::dropIfExists('tb_lowongan_pekerjaan');
        
        Schema::create('tb_lowongan_pekerjaan', function (Blueprint $table) {
            $table->increments('id_lowongan_pekerjaan');
            $table->string('judul_pekerjaan', 100);
            $table->unsignedInteger('id_posisi');
            $table->unsignedInteger('jumlah_lowongan');
            $table->string('pengalaman_minimal', 50)->nullable();
            $table->decimal('gaji_minimal', 12, 2)->nullable();
            $table->decimal('gaji_maksimal', 12, 2)->nullable();
            $table->string('status', 20)->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('deskripsi')->nullable();
            $table->text('persyaratan')->nullable();
            $table->timestamps();
            
            $table->foreign('id_posisi')->references('id_posisi')->on('tb_posisi');
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
