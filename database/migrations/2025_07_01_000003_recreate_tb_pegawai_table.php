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
        Schema::dropIfExists('tb_pegawai');
        
        Schema::create('tb_pegawai', function (Blueprint $table) {
            $table->increments('id_pegawai');
            $table->unsignedInteger('id_user')->nullable();
            $table->string('nama_lengkap', 100);
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('NIP', 20)->nullable();
            $table->string('NIK', 16)->nullable()->unique();
            $table->unsignedInteger('id_posisi')->nullable();
            $table->string('agama', 20)->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->timestamps();
            
            $table->foreign('id_user')->references('id_user')->on('tb_user');
            $table->foreign('id_posisi')->references('id_posisi')->on('tb_posisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pegawai');
    }
};
