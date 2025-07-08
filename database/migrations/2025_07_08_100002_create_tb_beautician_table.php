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
        Schema::create('tb_beautician', function (Blueprint $table) {
            $table->increments('id_beautician');
            $table->unsignedInteger('id_pegawai');
            $table->string('nama_beautician', 50);
            $table->string('no_telp', 50);
            $table->string('email_beautician', 50);
            $table->string('NIP', 50);
            $table->timestamps();
            
            $table->foreign('id_pegawai')->references('id_pegawai')->on('tb_pegawai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_beautician');
    }
};
