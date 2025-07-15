<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dokter;

class DokterSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'id_pegawai' => 3, // Dr. Ahmad Zulkarnaen
                'nama_dokter' => 'Dr. Ahmad Zulkarnaen, Sp.KK',
                'no_telp' => '081234567892',
                'email_dokter' => 'dokter1@klinik.com',
                'NIP' => 'DOK001',
                'foto_dokter' => 'dokter/dr_ahmad.jpg',
            ],
            [
                'id_pegawai' => 4, // Dr. Sari Handayani
                'nama_dokter' => 'Dr. Sari Handayani, Sp.DV',
                'no_telp' => '081234567893',
                'email_dokter' => 'dokter2@klinik.com',
                'NIP' => 'DOK002',
                'foto_dokter' => 'dokter/dr_sari.jpg',
            ],
        ];

        foreach ($doctors as $doctorData) {
            Dokter::create($doctorData);
        }
    }
}
