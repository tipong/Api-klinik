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
        Schema::create('tb_posisi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_posisi', 100);
            $table->string('departemen', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('tunjangan', 15, 2)->nullable();
            $table->integer('kuota')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_posisi');
    }
};
