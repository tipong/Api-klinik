<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absensi;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        Absensi::truncate();
        
        // Generate attendance data for the last 3 months
        $this->generateAttendanceForMonths();
    }
    
    private function generateAttendanceForMonths()
    {
        // Generate for June, July, August 2025
        $months = [
            ['year' => 2025, 'month' => 5], // May
            ['year' => 2025, 'month' => 6], // June  
            ['year' => 2025, 'month' => 7], // July
        ];
        
        // All active employee IDs
        $pegawaiIds = [1, 2, 3, 4, 5, 6, 7, 8];
        
        foreach ($months as $period) {
            $this->generateMonthlyAttendance($period['year'], $period['month'], $pegawaiIds);
        }
    }
    
    private function generateMonthlyAttendance($year, $month, $pegawaiIds)
    {
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        // Generate attendance for each weekday in the month
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            // Only create attendance for weekdays (Monday to Friday)
            if ($currentDate->isWeekday()) {
                foreach ($pegawaiIds as $pegawaiId) {
                    // Random attendance pattern - 85% chance of attendance
                    $isPresent = rand(1, 100) <= 85;
                    
                    if ($isPresent) {
                        // Random arrival time between 08:00 and 09:00
                        $jamMasuk = $this->randomTime(8, 0, 9, 0);
                        // Random departure time between 17:00 and 18:00  
                        $jamKeluar = $this->randomTime(17, 0, 18, 0);
                        
                        Absensi::create([
                            'id_pegawai' => $pegawaiId,
                            'tanggal_absensi' => $currentDate->format('Y-m-d'),
                            'jam_masuk' => $jamMasuk,
                            'jam_keluar' => $jamKeluar,
                            'status' => 'Hadir',
                            'keterangan' => $this->getRandomKeterangan(),
                        ]);
                    } else {
                        // Absent with random reason
                        $absentStatuses = ['Sakit', 'Izin', 'Alpa'];
                        $status = $absentStatuses[array_rand($absentStatuses)];
                        
                        Absensi::create([
                            'id_pegawai' => $pegawaiId,
                            'tanggal_absensi' => $currentDate->format('Y-m-d'),
                            'jam_masuk' => null,
                            'jam_keluar' => null,
                            'status' => $status,
                            'keterangan' => $this->getAbsentKeterangan($status),
                        ]);
                    }
                }
            }
            $currentDate->addDay();
        }
    }
    
    private function randomTime($startHour, $startMinute, $endHour, $endMinute)
    {
        $start = $startHour * 60 + $startMinute;
        $end = $endHour * 60 + $endMinute;
        $randomMinutes = rand($start, $end);
        
        $hour = intval($randomMinutes / 60);
        $minute = $randomMinutes % 60;
        
        return sprintf('%02d:%02d:00', $hour, $minute);
    }
    
    private function getRandomKeterangan()
    {
        $keterangans = [
            'Datang tepat waktu',
            'Datang sedikit terlambat',
            'Meeting pagi',
            'Shift normal',
            'Overtime',
            'Jadwal khusus',
        ];
        
        return $keterangans[array_rand($keterangans)];
    }
    
    private function getAbsentKeterangan($status)
    {
        $keterangans = [
            'Sakit' => ['Demam', 'Flu', 'Sakit kepala', 'Checkup kesehatan'],
            'Izin' => ['Keperluan keluarga', 'Urusan pribadi', 'Acara keluarga', 'Keperluan penting'],
            'Alpa' => ['Tidak ada kabar', 'Tanpa keterangan', 'Tidak konfirmasi'],
        ];
        
        $list = $keterangans[$status];
        return $list[array_rand($list)];
    }
}
