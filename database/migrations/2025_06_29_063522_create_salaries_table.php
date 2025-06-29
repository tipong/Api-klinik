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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->year('tahun'); // Tahun gaji
            $table->integer('bulan'); // Bulan gaji (1-12)
            $table->date('periode_mulai'); // Periode gaji mulai
            $table->date('periode_selesai'); // Periode gaji selesai
            
            // Komponen gaji
            $table->decimal('gaji_pokok', 15, 2)->default(0); // Gaji pokok
            $table->decimal('tunjangan_tetap', 15, 2)->default(0); // Tunjangan tetap
            $table->decimal('tunjangan_kehadiran', 15, 2)->default(0); // Tunjangan kehadiran
            $table->decimal('bonus_kinerja', 15, 2)->default(0); // Bonus kinerja
            $table->decimal('bonus_penjualan', 15, 2)->default(0); // Bonus penjualan
            $table->decimal('uang_lembur', 15, 2)->default(0); // Uang lembur
            $table->decimal('tunjangan_lain', 15, 2)->default(0); // Tunjangan lainnya
            
            // Potongan
            $table->decimal('potongan_terlambat', 15, 2)->default(0); // Potongan keterlambatan
            $table->decimal('potongan_alpha', 15, 2)->default(0); // Potongan alpha/tidak masuk
            $table->decimal('potongan_bpjs', 15, 2)->default(0); // Potongan BPJS
            $table->decimal('potongan_pajak', 15, 2)->default(0); // Potongan pajak
            $table->decimal('potongan_lain', 15, 2)->default(0); // Potongan lainnya
            
            // Perhitungan kehadiran
            $table->integer('hari_kerja')->default(0); // Hari kerja dalam periode
            $table->integer('hari_hadir')->default(0); // Hari hadir
            $table->integer('hari_terlambat')->default(0); // Hari terlambat
            $table->integer('hari_alpha')->default(0); // Hari alpha
            $table->integer('hari_izin')->default(0); // Hari izin
            $table->integer('hari_sakit')->default(0); // Hari sakit
            $table->integer('total_menit_lembur')->default(0); // Total menit lembur
            
            // Total
            $table->decimal('total_pendapatan', 15, 2)->default(0); // Total pendapatan
            $table->decimal('total_potongan', 15, 2)->default(0); // Total potongan
            $table->decimal('gaji_bersih', 15, 2)->default(0); // Gaji bersih
            
            // Status
            $table->enum('status', ['draft', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->date('tanggal_dibayar')->nullable(); // Tanggal pembayaran
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'tahun', 'bulan']); // Satu gaji per bulan per user
            $table->index(['tahun', 'bulan', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
