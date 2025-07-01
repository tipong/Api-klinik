<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Posisi;
use App\Models\Pegawai;
use App\Models\Absensi;
use App\Models\LowonganPekerjaan;

class HRTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::create([
            'nama_user' => 'Admin HR',
            'no_telp' => '081234567890',
            'email' => 'admin@klinik.com',
            'tanggal_lahir' => '1990-01-01',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create HRD user
        $hrdUser = User::create([
            'nama_user' => 'HRD Manager',
            'no_telp' => '081234567891',
            'email' => 'hrd@klinik.com',
            'tanggal_lahir' => '1985-05-15',
            'password' => Hash::make('password123'),
            'role' => 'hrd',
        ]);

        // Create employee user
        $employeeUser = User::create([
            'nama_user' => 'Dokter Ahmad',
            'no_telp' => '081234567892',
            'email' => 'dokter@klinik.com',
            'tanggal_lahir' => '1988-03-20',
            'password' => Hash::make('password123'),
            'role' => 'dokter',
        ]);

        // Create positions
        $posisiDokter = Posisi::create([
            'nama_posisi' => 'Dokter',
            'gaji_pokok' => 15000000.00,
            'persen_bonus' => 10.00,
        ]);

        $posisiHRD = Posisi::create([
            'nama_posisi' => 'HRD Manager',
            'gaji_pokok' => 8000000.00,
            'persen_bonus' => 5.00,
        ]);

        $posisiFrontOffice = Posisi::create([
            'nama_posisi' => 'Front Office',
            'gaji_pokok' => 5000000.00,
            'persen_bonus' => 3.00,
        ]);

        // Create employees
        $pegawaiAdmin = Pegawai::create([
            'id_user' => $adminUser->id_user,
            'nama_lengkap' => 'Admin HR',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jalan Admin No. 1, Jakarta',
            'telepon' => '081234567890',
            'email' => 'admin@klinik.com',
            'NIP' => 'ADM001',
            'NIK' => '1234567890123456',
            'id_posisi' => $posisiHRD->id_posisi,
            'agama' => 'Islam',
            'tanggal_masuk' => '2024-01-01',
        ]);

        $pegawaiDokter = Pegawai::create([
            'id_user' => $employeeUser->id_user,
            'nama_lengkap' => 'Dr. Ahmad Zulkarnaen',
            'tanggal_lahir' => '1988-03-20',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jalan Dokter No. 5, Jakarta',
            'telepon' => '081234567892',
            'email' => 'dokter@klinik.com',
            'NIP' => 'DOK001',
            'NIK' => '1234567890123457',
            'id_posisi' => $posisiDokter->id_posisi,
            'agama' => 'Islam',
            'tanggal_masuk' => '2024-02-01',
        ]);

        // Create attendance records
        Absensi::create([
            'id_pegawai' => $pegawaiDokter->id_pegawai,
            'tanggal' => now()->format('Y-m-d'),
        ]);

        Absensi::create([
            'id_pegawai' => $pegawaiAdmin->id_pegawai,
            'tanggal' => now()->format('Y-m-d'),
        ]);

        // Create job postings
        LowonganPekerjaan::create([
            'id_posisi' => $posisiFrontOffice->id_posisi,
            'judul_pekerjaan' => 'Front Office Staff',
            'jumlah_lowongan' => 2,
            'pengalaman_minimal' => '1 tahun',
            'gaji_minimal' => 4000000.00,
            'gaji_maksimal' => 6000000.00,
            'status' => 'aktif',
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(30)->format('Y-m-d'),
            'deskripsi' => 'Dicari Front Office Staff yang berpengalaman untuk klinik kecantikan.',
            'persyaratan' => '- Min. SMA/SMK\n- Pengalaman 1 tahun\n- Komunikasi baik\n- Berpenampilan menarik',
        ]);
    }
}
