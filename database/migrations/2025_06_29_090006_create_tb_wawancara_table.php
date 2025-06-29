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
        Schema::create('tb_wawancara', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lamaran_id');
            $table->dateTime('jadwal_wawancara');
            $table->string('lokasi', 255);
            $table->string('pewawancara', 100);
            $table->enum('metode', ['Tatap Muka', 'Online', 'Telepon'])->default('Tatap Muka');
            $table->enum('status', ['Terjadwal', 'Selesai', 'Batal', 'Pending'])->default('Terjadwal');
            $table->text('catatan')->nullable();
            $table->enum('hasil', ['Lulus', 'Tidak Lulus', 'Pending'])->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->foreign('lamaran_id')->references('id')->on('tb_lamaran_pekerjaan');
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
