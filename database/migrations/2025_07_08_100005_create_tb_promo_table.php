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
        Schema::create('tb_promo', function (Blueprint $table) {
            $table->increments('id_promo');
            $table->string('nama_promo', 255);
            $table->enum('jenis_promo', ['Treatment', 'Produk']);
            $table->text('deskripsi_promo');
            $table->enum('tipe_potongan', ['Diskon', 'Rupiah']);
            $table->decimal('potongan_harga', 15, 2);
            $table->decimal('minimal_belanja', 15, 2)->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->string('gambar_promo', 255);
            $table->enum('status_promo', ['Aktif', 'Tidak Aktif']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_promo');
    }
};
