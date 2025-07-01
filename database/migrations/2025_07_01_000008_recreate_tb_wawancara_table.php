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
        Schema::dropIfExists('tb_wawancara');
        
        Schema::create('tb_wawancara', function (Blueprint $table) {
            $table->increments('id_wawancara');
            $table->unsignedInteger('id_lamaran_pekerjaan');
            $table->unsignedInteger('id_user');
            $table->dateTime('tanggal_wawancara');
            $table->string('lokasi', 255);
            $table->text('catatan')->nullable();
            $table->enum('hasil', ['diterima', 'ditolak', 'pending'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default('1970-01-01 00:00:01');
            
            $table->foreign('id_lamaran_pekerjaan')->references('id_lamaran_pekerjaan')->on('tb_lamaran_pekerjaan');
            $table->foreign('id_user')->references('id_user')->on('tb_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_wawancara');
    }
};
