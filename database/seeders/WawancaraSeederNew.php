<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wawancara;
use Carbon\Carbon;

class WawancaraSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interviews = [
            [
                'id_lamaran_pekerjaan' => 1, // Siti Aminah - Front Office
                'id_user' => 2, // HRD yang melakukan wawancara
                'tanggal_wawancara' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'),
                'lokasi' => 'Ruang Meeting Klinik',
                'catatan' => 'Kandidat memiliki pengalaman customer service yang baik',
                'status' => 'pending',
            ],
            [
                'id_lamaran_pekerjaan' => 2, // Andi Pratama - Front Office
                'id_user' => 2, // HRD yang melakukan wawancara
                'tanggal_wawancara' => Carbon::yesterday()->format('Y-m-d H:i:s'),
                'lokasi' => 'Ruang Meeting Klinik',
                'catatan' => 'Kandidat sangat komunikatif dan memiliki pengalaman di bidang kesehatan',
                'status' => 'pending',
            ],
            [
                'id_lamaran_pekerjaan' => 3, // Maya Sari - Beautician
                'id_user' => 2, // HRD yang melakukan wawancara
                'tanggal_wawancara' => Carbon::now()->subDays(2)->format('Y-m-d H:i:s'),
                'lokasi' => 'Ruang Treatment',
                'catatan' => 'Kurang pengalaman dalam teknik perawatan wajah modern',
                'status' => 'ditolak',
            ],
            [
                'id_lamaran_pekerjaan' => 4, // Dewi Kusuma - Beautician
                'id_user' => 2, // HRD yang melakukan wawancara
                'tanggal_wawancara' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
                'lokasi' => 'Ruang Treatment',
                'catatan' => 'Perlu evaluasi praktik langsung',
                'status' => 'pending',
            ],
            [
                'id_lamaran_pekerjaan' => 5, // Rudi Hermawan - Kasir
                'id_user' => 2, // HRD yang melakukan wawancara
                'tanggal_wawancara' => Carbon::now()->subDays(1)->format('Y-m-d H:i:s'),
                'lokasi' => 'Ruang Kasir',
                'catatan' => 'Kandidat memiliki pengalaman kasir dan cepat dalam menghitung',
                'status' => 'pending',
            ],
        ];

        foreach ($interviews as $interviewData) {
            Wawancara::create($interviewData);
        }
    }
}
