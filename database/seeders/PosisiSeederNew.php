<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Posisi;

class PosisiSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'nama_posisi' => 'Dokter ',
                'gaji_pokok' => 20000000.00,
                'persen_bonus' => 5.00,
            ],
            [
                'nama_posisi' => 'Beautician',
                'gaji_pokok' => 8000000.00,
                'persen_bonus' => 3.00,
            ],
            [
                'nama_posisi' => 'HRD Manager',
                'gaji_pokok' => 12000000.00,
                'persen_bonus' => 0.00,
            ],
            [
                'nama_posisi' => 'Front Office',
                'gaji_pokok' => 5000000.00,
                'persen_bonus' => 0.00,
            ],
            [
                'nama_posisi' => 'Kasir',
                'gaji_pokok' => 4500000.00,
                'persen_bonus' => 0.00,
            ],
            [
                'nama_posisi' => 'Admin',
                'gaji_pokok' => 7000000.00,
                'persen_bonus' => 0.00,
            ],
        ];

        foreach ($positions as $positionData) {
            Posisi::create($positionData);
        }
    }
}
