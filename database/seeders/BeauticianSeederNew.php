<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Beautician;

class BeauticianSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $beauticians = [
            [
                'id_pegawai' => 5, // Maria Gonzales
                'nama_beautician' => 'Maria Gonzales',
                'no_telp' => '081234567894',
                'email_beautician' => 'beautician1@klinik.com',
                'NIP' => 'BTC001',
            ],
            [
                'id_pegawai' => 6, // Linda Sari Dewi
                'nama_beautician' => 'Linda Sari Dewi',
                'no_telp' => '081234567895',
                'email_beautician' => 'beautician2@klinik.com',
                'NIP' => 'BTC002',
            ],
        ];

        foreach ($beauticians as $beauticianData) {
            Beautician::create($beauticianData);
        }
    }
}
