<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gaji;
use App\Models\Pegawai;
use App\Models\Absensi;
use App\Models\BookingTreatment;
use Carbon\Carbon;

class GajiSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua data gaji yang ada
        Gaji::truncate();
        
        echo "🗑️  Data gaji lama telah dihapus.\n";
        
        // Generate data absensi untuk 3 bulan terakhir jika belum ada
        $this->generateAbsensiData();
        
        // Generate data booking treatment untuk 3 bulan terakhir jika belum ada  
        $this->generateBookingTreatmentData();
        
        echo "✅ Data pendukung untuk generate gaji telah disiapkan.\n";
        echo "💰 Sekarang Anda dapat menggunakan API generate gaji massal.\n";
        echo "📋 Endpoint: POST /api/gaji/generate\n";
        echo "📅 Parameter: periode_bulan (1-12), periode_tahun (contoh: 2024)\n";
    }
    
    /**
     * Generate data absensi untuk testing
     */
    private function generateAbsensiData()
    {
        $pegawaiList = Pegawai::all();
        
        if ($pegawaiList->isEmpty()) {
            echo "❌ Data pegawai tidak ditemukan. Jalankan PegawaiSeederNew terlebih dahulu.\n";
            return;
        }
        
        // Generate absensi untuk 3 bulan terakhir
        for ($monthOffset = 2; $monthOffset >= 0; $monthOffset--) {
            $targetMonth = Carbon::now()->subMonths($monthOffset);
            $startDate = $targetMonth->copy()->startOfMonth();
            $endDate = $targetMonth->copy()->endOfMonth();
            
            echo "📅 Generating absensi untuk {$targetMonth->format('F Y')}...\n";
            
            foreach ($pegawaiList as $pegawai) {
                $currentDate = $startDate->copy();
                
                while ($currentDate <= $endDate) {
                    // Hanya buat absensi untuk hari kerja (Senin-Jumat)
                    if ($currentDate->isWeekday()) {
                        // 90% kemungkinan hadir, 10% tidak hadir
                        $isPresent = rand(1, 100) <= 90;
                        
                        if ($isPresent) {
                            // Cek apakah absensi sudah ada
                            $existingAbsensi = Absensi::where('id_pegawai', $pegawai->id_pegawai)
                                                    ->where('tanggal', $currentDate->format('Y-m-d'))
                                                    ->first();
                            
                            if (!$existingAbsensi) {
                                Absensi::create([
                                    'id_pegawai' => $pegawai->id_pegawai,
                                    'tanggal' => $currentDate->format('Y-m-d'),
                                    'jam_masuk' => $currentDate->copy()->setTime(8, rand(0, 30))->format('H:i:s'),
                                    'jam_keluar' => $currentDate->copy()->setTime(17, rand(0, 30))->format('H:i:s'),
                                    'status' => 'Hadir',
                                ]);
                            }
                        }
                    }
                    
                    $currentDate->addDay();
                }
            }
        }
        
        echo "✅ Data absensi berhasil digenerate.\n";
    }
    
    /**
     * Generate data booking treatment untuk testing
     */
    private function generateBookingTreatmentData()
    {
        // Cek apakah sudah ada data booking treatment
        $existingBookings = BookingTreatment::count();
        
        if ($existingBookings > 0) {
            echo "📋 Data booking treatment sudah ada ({$existingBookings} records).\n";
            return;
        }
        
        $pegawaiList = Pegawai::whereHas('posisi', function($query) {
            $query->whereIn('nama_posisi', [
                'Dokter Spesialis', 
                'Dokter Umum', 
                'Beautician Senior', 
                'Beautician Junior'
            ]);
        })->get();
        
        if ($pegawaiList->isEmpty()) {
            echo "❌ Data pegawai dengan posisi dokter/beautician tidak ditemukan.\n";
            return;
        }
        
        // Generate booking treatment untuk 3 bulan terakhir
        for ($monthOffset = 2; $monthOffset >= 0; $monthOffset--) {
            $targetMonth = Carbon::now()->subMonths($monthOffset);
            $startDate = $targetMonth->copy()->startOfMonth();
            $endDate = $targetMonth->copy()->endOfMonth();
            
            echo "💆 Generating booking treatment untuk {$targetMonth->format('F Y')}...\n";
            
            // Generate 50-100 booking per bulan
            $bookingCount = rand(50, 100);
            
            for ($i = 0; $i < $bookingCount; $i++) {
                $randomDate = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                );
                
                $pegawai = $pegawaiList->random();
                $hargaTotal = rand(500000, 5000000); // 500k - 5jt
                
                // Tentukan apakah dokter atau beautician
                $isDokter = in_array($pegawai->posisi->nama_posisi, ['Dokter Spesialis', 'Dokter Umum']);
                
                BookingTreatment::create([
                    'id_dokter' => $isDokter ? $pegawai->id_pegawai : null,
                    'id_beautician' => !$isDokter ? $pegawai->id_pegawai : null,
                    'waktu_treatment' => $randomDate->format('Y-m-d H:i:s'),
                    'harga_total' => $hargaTotal,
                    'status_booking_treatment' => 'Selesai',
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);
            }
        }
        
        echo "✅ Data booking treatment berhasil digenerate.\n";
    }
}
