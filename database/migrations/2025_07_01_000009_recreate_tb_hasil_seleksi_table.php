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
        Schema::dropIfExists('tb_hasil_seleksi');
        
        Schema::create('tb_hasil_seleksi', function (Blueprint $table) {
            $table->increments('id_hasil_seleksi');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_lowongan_pekerjaan');
            $table->enum('status', ['diterima', 'ditolak', 'pending'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default('1970-01-01 00:00:01');
            
            $table->foreign('id_user')->references('id_user')->on('tb_user');
            $table->foreign('id_lowongan_pekerjaan')->references('id_lowongan_pekerjaan')->on('tb_lowongan_pekerjaan');
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
