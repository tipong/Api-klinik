<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absensi;
use Carbon\Carbon;

class AbsensiSeederNew extends Seeder
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
                        $absentStatuses = ['Sakit', 'Izin', 'Alpha'];
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
            'Alpha' => ['Tidak ada kabar', 'Tanpa keterangan', 'Tidak konfirmasi'],
        ];
        
        $list = $keterangans[$status];
        return $list[array_rand($list)];
    }
                'status' => 'Hadir',
                'keterangan' => 'Jadwal dokter mulai jam 9:30',
            ],
            [
                'id_pegawai' => 5, // Maria Beautician
                'jam_masuk' => '08:30:00',
                'jam_keluar' => null, // Still working
                'tanggal_absensi' => $today->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Datang sesuai jadwal beautician',
            ],
            [
                'id_pegawai' => 6, // Linda Beautician
                'jam_masuk' => null,
                'jam_keluar' => null,
                'tanggal_absensi' => $today->format('Y-m-d'),
                'status' => 'Sakit',
                'keterangan' => 'Demam dan flu',
            ],
            [
                'id_pegawai' => 7, // Rina Front Office
                'jam_masuk' => '07:45:00',
                'jam_keluar' => null, // Still working
                'tanggal_absensi' => $today->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Datang lebih awal untuk persiapan',
            ],
            [
                'id_pegawai' => 8, // Devi Kasir
                'jam_masuk' => '08:00:00',
                'jam_keluar' => null, // Still working
                'tanggal_absensi' => $today->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Datang tepat waktu',
            ],

            // Yesterday's attendance (complete day)
            [
                'id_pegawai' => 1,
                'jam_masuk' => '08:00:00',
                'jam_keluar' => '17:00:00',
                'tanggal_absensi' => $yesterday->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Kerja hari penuh',
            ],
            [
                'id_pegawai' => 2,
                'jam_masuk' => '08:10:00',
                'jam_keluar' => '17:15:00',
                'tanggal_absensi' => $yesterday->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Kerja hari penuh',
            ],
            [
                'id_pegawai' => 3,
                'jam_masuk' => '09:00:00',
                'jam_keluar' => '18:00:00',
                'tanggal_absensi' => $yesterday->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Jadwal dokter',
            ],
            [
                'id_pegawai' => 4,
                'jam_masuk' => '09:15:00',
                'jam_keluar' => '17:30:00',
                'tanggal_absensi' => $yesterday->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Jadwal dokter',
            ],
            [
                'id_pegawai' => 5,
                'jam_masuk' => '08:30:00',
                'jam_keluar' => '17:00:00',
                'tanggal_absensi' => $yesterday->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Kerja hari penuh',
            ],
            [
                'id_pegawai' => 6,
                'jam_masuk' => '08:45:00',
                'jam_keluar' => '17:15:00',
                'tanggal_absensi' => $yesterday->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Kerja hari penuh',
            ],
            [
                'id_pegawai' => 7,
                'jam_masuk' => '07:50:00',
                'jam_keluar' => '16:50:00',
                'tanggal_absensi' => $yesterday->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Kerja hari penuh',
            ],
            [
                'id_pegawai' => 8,
                'jam_masuk' => null,
                'jam_keluar' => null,
                'tanggal_absensi' => $yesterday->format('Y-m-d'),
                'status' => 'Izin',
                'keterangan' => 'Izin keperluan keluarga',
            ],

            // Two days ago attendance
            [
                'id_pegawai' => 1,
                'jam_masuk' => '08:05:00',
                'jam_keluar' => '17:10:00',
                'tanggal_absensi' => $twoDaysAgo->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Kerja hari penuh',
            ],
            [
                'id_pegawai' => 2,
                'jam_masuk' => '08:20:00',
                'jam_keluar' => '17:20:00',
                'tanggal_absensi' => $twoDaysAgo->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Kerja hari penuh',
            ],
            [
                'id_pegawai' => 3,
                'jam_masuk' => null,
                'jam_keluar' => null,
                'tanggal_absensi' => $twoDaysAgo->format('Y-m-d'),
                'status' => 'Alpa',
                'keterangan' => 'Tidak ada kabar',
            ],
            [
                'id_pegawai' => 4,
                'jam_masuk' => '09:30:00',
                'jam_keluar' => '17:45:00',
                'tanggal_absensi' => $twoDaysAgo->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Jadwal dokter',
            ],
            [
                'id_pegawai' => 5,
                'jam_masuk' => '08:25:00',
                'jam_keluar' => '17:05:00',
                'tanggal_absensi' => $twoDaysAgo->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Kerja hari penuh',
            ],
            [
                'id_pegawai' => 6,
                'jam_masuk' => '08:40:00',
                'jam_keluar' => '17:10:00',
                'tanggal_absensi' => $twoDaysAgo->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Kerja hari penuh',
            ],
            [
                'id_pegawai' => 7,
                'jam_masuk' => '07:55:00',
                'jam_keluar' => '16:55:00',
                'tanggal_absensi' => $twoDaysAgo->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Kerja hari penuh',
            ],
            [
                'id_pegawai' => 8,
                'jam_masuk' => '08:00:00',
                'jam_keluar' => '17:00:00',
                'tanggal_absensi' => $twoDaysAgo->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Kerja hari penuh',
            ],
        ];

        foreach ($attendanceRecords as $record) {
            Absensi::create($record);
        }
    }
}
