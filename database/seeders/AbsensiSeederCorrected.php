<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absensi;
use Carbon\Carbon;

class AbsensiSeederCorrected extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $twoDaysAgo = Carbon::today()->subDays(2);
        
        $attendanceRecords = [
            // Today's attendance
            [
                'id_pegawai' => 1, // Admin
                'jam_masuk' => '08:00:00',
                'jam_keluar' => null, // Still working
                'tanggal_absensi' => $today->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Datang tepat waktu',
            ],
            [
                'id_pegawai' => 2, // HRD
                'jam_masuk' => '08:15:00',
                'jam_keluar' => null, // Still working
                'tanggal_absensi' => $today->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Datang sedikit terlambat',
            ],
            [
                'id_pegawai' => 3, // Dr. Ahmad
                'jam_masuk' => '09:00:00',
                'jam_keluar' => null, // Still working
                'tanggal_absensi' => $today->format('Y-m-d'),
                'status' => 'Hadir',
                'keterangan' => 'Jadwal dokter mulai jam 9',
            ],
            [
                'id_pegawai' => 4, // Dr. Sari
                'jam_masuk' => '09:30:00',
                'jam_keluar' => null, // Still working
                'tanggal_absensi' => $today->format('Y-m-d'),
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
