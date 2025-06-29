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
        Schema::create('tb_pegawai', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('posisi_id');
            $table->string('nip', 50)->unique();
            $table->string('nama', 100);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('alamat', 255)->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('pendidikan_terakhir', 50)->nullable();
            $table->date('tanggal_masuk');
            $table->enum('status_kepegawaian', ['Tetap', 'Kontrak', 'Magang']);
            $table->enum('status_aktif', ['Aktif', 'Nonaktif', 'Cuti']);
            $table->string('photo', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('tb_user');
            $table->foreign('posisi_id')->references('id')->on('tb_posisi');
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
