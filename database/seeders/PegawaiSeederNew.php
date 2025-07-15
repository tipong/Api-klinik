<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\Posisi;

class PegawaiSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'id_user' => 1, // Super Admin
                'nama_lengkap' => 'Ahmad Supardi',
                'tanggal_lahir' => '1985-01-15',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'telepon' => '081234567890',
                'email' => 'admin@klinik.com',
                'NIP' => 'ADM001',
                'NIK' => '3171012345678901',
                'id_posisi' => 10, // Admin
                'agama' => 'Islam',
                'tanggal_masuk' => '2024-01-01',
            ],
            [
                'id_user' => 2, // HRD Manager
                'nama_lengkap' => 'Siti Nurhaliza',
                'tanggal_lahir' => '1987-03-22',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Thamrin No. 456, Jakarta Pusat',
                'telepon' => '081234567891',
                'email' => 'hrd@klinik.com',
                'NIP' => 'HRD001',
                'NIK' => '3171012345678902',
                'id_posisi' => 5, // HRD Manager
                'agama' => 'Islam',
                'tanggal_masuk' => '2024-01-15',
            ],
            [
                'id_user' => 3, // Dr. Ahmad
                'nama_lengkap' => 'Dr. Ahmad Zulkarnaen, Sp.KK',
                'tanggal_lahir' => '1980-05-10',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Menteng Raya No. 789, Jakarta Pusat',
                'telepon' => '081234567892',
                'email' => 'dokter1@klinik.com',
                'NIP' => 'DOK001',
                'NIK' => '3171012345678903',
                'id_posisi' => 1, // Dokter Spesialis
                'agama' => 'Islam',
                'tanggal_masuk' => '2024-02-01',
            ],
            [
                'id_user' => 4, // Dr. Sari
                'nama_lengkap' => 'Dr. Sari Handayani, Sp.DV',
                'tanggal_lahir' => '1983-08-25',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Kemang Raya No. 321, Jakarta Selatan',
                'telepon' => '081234567893',
                'email' => 'dokter2@klinik.com',
                'NIP' => 'DOK002',
                'NIK' => '3171012345678904',
                'id_posisi' => 1, // Dokter Spesialis
                'agama' => 'Kristen',
                'tanggal_masuk' => '2024-02-15',
            ],
            [
                'id_user' => 5, // Maria Beautician
                'nama_lengkap' => 'Maria Gonzales',
                'tanggal_lahir' => '1990-02-14',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Senopati No. 654, Jakarta Selatan',
                'telepon' => '081234567894',
                'email' => 'beautician1@klinik.com',
                'NIP' => 'BTC001',
                'NIK' => '3171012345678905',
                'id_posisi' => 3, // Beautician Senior
                'agama' => 'Katolik',
                'tanggal_masuk' => '2024-03-01',
            ],
            [
                'id_user' => 6, // Linda Beautician
                'nama_lengkap' => 'Linda Sari Dewi',
                'tanggal_lahir' => '1992-07-18',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Kuningan No. 987, Jakarta Selatan',
                'telepon' => '081234567895',
                'email' => 'beautician2@klinik.com',
                'NIP' => 'BTC002',
                'NIK' => '3171012345678906',
                'id_posisi' => 4, // Beautician Junior
                'agama' => 'Islam',
                'tanggal_masuk' => '2024-03-15',
            ],
            [
                'id_user' => 7, // Rina Front Office
                'nama_lengkap' => 'Rina Maharani',
                'tanggal_lahir' => '1995-04-12',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Pancoran No. 147, Jakarta Selatan',
                'telepon' => '081234567896',
                'email' => 'frontoffice@klinik.com',
                'NIP' => 'FO001',
                'NIK' => '3171012345678907',
                'id_posisi' => 6, // Front Office
                'agama' => 'Islam',
                'tanggal_masuk' => '2024-04-01',
            ],
            [
                'id_user' => 8, // Devi Kasir
                'nama_lengkap' => 'Devi Anggraini',
                'tanggal_lahir' => '1993-09-30',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Tebet No. 258, Jakarta Selatan',
                'telepon' => '081234567897',
                'email' => 'kasir@klinik.com',
                'NIP' => 'KSR001',
                'NIK' => '3171012345678908',
                'id_posisi' => 7, // Kasir
                'agama' => 'Islam',
                'tanggal_masuk' => '2024-04-15',
            ],
        ];

        foreach ($employees as $employeeData) {
            Pegawai::create($employeeData);
        }
    }
}
