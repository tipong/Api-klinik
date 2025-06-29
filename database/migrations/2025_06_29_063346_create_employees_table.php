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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('employee_id')->unique(); // ID karyawan
            $table->string('nip')->unique()->nullable(); // NIP pegawai
            $table->string('departemen')->nullable();
            $table->string('jabatan')->nullable();
            $table->date('tanggal_bergabung'); // Tanggal bergabung
            $table->decimal('gaji_pokok', 15, 2)->default(0); // Gaji pokok
            $table->decimal('tunjangan_tetap', 15, 2)->default(0); // Tunjangan tetap
            $table->enum('status_kerja', ['tetap', 'kontrak', 'magang'])->default('kontrak');
            $table->date('kontrak_mulai')->nullable();
            $table->date('kontrak_selesai')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
