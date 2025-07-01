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
        Schema::dropIfExists('tb_pelatihan');
        
        Schema::create('tb_pelatihan', function (Blueprint $table) {
            $table->increments('id_pelatihan');
            $table->string('judul', 100);
            $table->text('deskripsi')->nullable();
            $table->string('jenis_pelatihan', 50)->nullable();
            $table->dateTime('jadwal_pelatihan')->nullable();
            $table->string('link_url', 255)->nullable();
            $table->unsignedInteger('durasi')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pelatihan');
    }
};
