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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal'); // Tanggal absensi
            $table->time('jam_masuk')->nullable(); // Jam check-in
            $table->time('jam_keluar')->nullable(); // Jam check-out
            
            // GPS Location untuk check-in
            $table->decimal('latitude_masuk', 10, 8)->nullable();
            $table->decimal('longitude_masuk', 11, 8)->nullable();
            $table->text('alamat_masuk')->nullable();
            $table->decimal('jarak_masuk', 8, 2)->nullable(); // Jarak dari kantor dalam meter
            
            // GPS Location untuk check-out
            $table->decimal('latitude_keluar', 10, 8)->nullable();
            $table->decimal('longitude_keluar', 11, 8)->nullable();
            $table->text('alamat_keluar')->nullable();
            $table->decimal('jarak_keluar', 8, 2)->nullable(); // Jarak dari kantor dalam meter
            
            $table->enum('status', ['hadir', 'terlambat', 'alpha', 'izin', 'sakit'])->default('hadir');
            $table->integer('menit_terlambat')->default(0); // Menit terlambat
            $table->integer('menit_lembur')->default(0); // Menit lembur
            $table->text('keterangan')->nullable();
            $table->boolean('is_approved')->default(false); // Approval dari HRD
            $table->timestamps();
            
            $table->unique(['user_id', 'tanggal']); // Satu absensi per hari per user
            $table->index(['tanggal', 'status']);
            $table->index(['user_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
