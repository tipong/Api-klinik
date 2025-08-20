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
        Schema::dropIfExists('tb_user');
        
        Schema::create('tb_user', function (Blueprint $table) {
            $table->increments('id_user');
            $table->string('nama_user', 255);
            $table->string('no_telp', 255)->unique();
            $table->string('email', 255)->unique();
            $table->date('tanggal_lahir')->nullable();
            $table->string('password', 255);
            $table->string('foto_profil', 255)->nullable();
            // SQLite doesn't need ENUM, just use string
            $table->string('role', 50)->default('pelanggan');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_user');
    }
};
