<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HasilSeleksi;

class HasilSeleksiSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $results = [
            [
                'id_user' => 9, // Budi (pelamar)
                'id_lamaran_pekerjaan' => 2, // Lamaran Andi Pratama yang diterima
                'status' => 'pending',
                'catatan' => 'Lulus seleksi administrasi dan wawancara. Memiliki pengalaman yang sesuai.',
            ],
            [
                'id_user' => 9, // Budi (pelamar)
                'id_lamaran_pekerjaan' => 3, // Lamaran Maya Sari yang ditolak
                'status' => 'ditolak',
                'catatan' => 'Tidak memenuhi syarat pengalaman minimum untuk posisi beautician.',
            ],
            [
                'id_user' => 10, // Sinta (pelamar)
                'id_lamaran_pekerjaan' => 5, // Lamaran Rudi Hermawan yang diterima
                'status' => 'pending',
                'catatan' => 'Lulus tes praktik kasir dan wawancara. Siap untuk onboarding.',
            ],
            [
                'id_user' => 9, // Budi (pelamar)
                'id_lamaran_pekerjaan' => 1, // Lamaran Siti Aminah masih pending
                'status' => 'pending',
                'catatan' => 'Menunggu hasil wawancara yang dijadwalkan.',
            ],
            [
                'id_user' => 10, // Sinta (pelamar)
                'id_lamaran_pekerjaan' => 4, // Lamaran Dewi Kusuma masih pending
                'status' => 'pending',
                'catatan' => 'Perlu evaluasi praktik beautician sebelum keputusan final.',
            ],
        ];

        foreach ($results as $resultData) {
            HasilSeleksi::create($resultData);
        }
    }
}
