<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LamaranPekerjaan;
use Carbon\Carbon;

class LamaranPekerjaanSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = [
            [
                'id_lowongan_pekerjaan' => 1, // Front Office
                'id_user' => 9, // Budi Pelanggan (user as applicant)
                'nama_pelamar' => 'Siti Aminah',
                'email_pelamar' => 'siti.aminah@gmail.com',
                'NIK_pelamar' => '3201015504950001',
                'telepon_pelamar' => '081234567901',
                'alamat_pelamar' => 'Jl. Kebon Jeruk No. 123, Jakarta Barat',
                'pendidikan_terakhir' => 'SMA',
                'CV' => null, // Binary data, using null for seeder
                'status' => 'pending',
            ],
            [
                'id_lowongan_pekerjaan' => 1, // Front Office
                'id_user' => 10, // Sinta Pelanggan (user as applicant)
                'nama_pelamar' => 'Andi Pratama',
                'email_pelamar' => 'andi.pratama@gmail.com',
                'NIK_pelamar' => '3201015203940001',
                'telepon_pelamar' => '081234567902',
                'alamat_pelamar' => 'Jl. Cempaka Putih No. 456, Jakarta Pusat',
                'pendidikan_terakhir' => 'SMK',
                'CV' => null,
                'status' => 'pending',
            ],
            [
                'id_lowongan_pekerjaan' => 2, // Beautician Junior
                'id_user' => 9, // Budi Pelanggan (user as applicant)
                'nama_pelamar' => 'Maya Sari',
                'email_pelamar' => 'maya.sari@gmail.com',
                'NIK_pelamar' => '3201015811970001',
                'telepon_pelamar' => '081234567903',
                'alamat_pelamar' => 'Jl. Melati No. 789, Jakarta Selatan',
                'pendidikan_terakhir' => 'SMK Kecantikan',
                'CV' => null,
                'status' => 'ditolak',
            ],
            [
                'id_lowongan_pekerjaan' => 2, // Beautician Junior
                'id_user' => 10, // Sinta Pelanggan (user as applicant)
                'nama_pelamar' => 'Dewi Kusuma',
                'email_pelamar' => 'dewi.kusuma@gmail.com',
                'NIK_pelamar' => '3201015209960001',
                'telepon_pelamar' => '081234567904',
                'alamat_pelamar' => 'Jl. Anggrek No. 321, Jakarta Timur',
                'pendidikan_terakhir' => 'D3 Tata Rias',
                'CV' => null,
                'status' => 'pending',
            ],
            [
                'id_lowongan_pekerjaan' => 3, // Kasir
                'id_user' => 9, // Budi Pelanggan (user as applicant)
                'nama_pelamar' => 'Rudi Hermawan',
                'email_pelamar' => 'rudi.hermawan@gmail.com',
                'NIK_pelamar' => '3201015212920001',
                'telepon_pelamar' => '081234567905',
                'alamat_pelamar' => 'Jl. Mawar No. 654, Jakarta Utara',
                'pendidikan_terakhir' => 'SMA',
                'CV' => null,
                'status' => 'pending',
            ],
            [
                'id_lowongan_pekerjaan' => 4, // Cleaning Service
                'id_user' => 10, // Sinta Pelanggan (user as applicant)
                'nama_pelamar' => 'Budi Santoso',
                'email_pelamar' => 'budi.santoso@gmail.com',
                'NIK_pelamar' => '3201015204880001',
                'telepon_pelamar' => '081234567906',
                'alamat_pelamar' => 'Jl. Kenanga No. 987, Jakarta Barat',
                'pendidikan_terakhir' => 'SMP',
                'CV' => null,
                'status' => 'pending',
            ],
        ];

        foreach ($applications as $applicationData) {
            LamaranPekerjaan::create($applicationData);
        }
    }
}
