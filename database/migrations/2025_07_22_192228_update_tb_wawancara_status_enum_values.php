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
        // For SQLite, we need to recreate the table to change enum constraint
        Schema::table('tb_wawancara', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['id_lamaran_pekerjaan']);
            $table->dropForeign(['id_user']);
        });

        // Recreate table with new enum values
        Schema::dropIfExists('tb_wawancara');
        
        Schema::create('tb_wawancara', function (Blueprint $table) {
            $table->increments('id_wawancara');
            $table->unsignedInteger('id_lamaran_pekerjaan');
            $table->unsignedInteger('id_user');
            $table->dateTime('tanggal_wawancara');
            $table->string('lokasi', 255);
            $table->text('catatan')->nullable();
            $table->enum('status', ['terjadwal', 'lulus', 'tidak_lulus'])->default('terjadwal');
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
        // Revert to old enum values
        Schema::table('tb_wawancara', function (Blueprint $table) {
            $table->dropForeign(['id_lamaran_pekerjaan']);
            $table->dropForeign(['id_user']);
        });

        Schema::dropIfExists('tb_wawancara');
        
        Schema::create('tb_wawancara', function (Blueprint $table) {
            $table->increments('id_wawancara');
            $table->unsignedInteger('id_lamaran_pekerjaan');
            $table->unsignedInteger('id_user');
            $table->dateTime('tanggal_wawancara');
            $table->string('lokasi', 255);
            $table->text('catatan')->nullable();
            $table->enum('status', ['diterima', 'ditolak', 'pending'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default('1970-01-01 00:00:01');
            
            $table->foreign('id_lamaran_pekerjaan')->references('id_lamaran_pekerjaan')->on('tb_lamaran_pekerjaan');
            $table->foreign('id_user')->references('id_user')->on('tb_user');
        });
    }
};
