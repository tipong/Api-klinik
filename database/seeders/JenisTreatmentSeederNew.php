<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisTreatment;

class JenisTreatmentSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $treatmentTypes = [
            [
                'nama_jenis_treatment' => 'Facial Treatment',
            ],
            [
                'nama_jenis_treatment' => 'Body Treatment',
            ],
            [
                'nama_jenis_treatment' => 'Anti Aging',
            ],
            [
                'nama_jenis_treatment' => 'Acne Treatment',
            ],
            [
                'nama_jenis_treatment' => 'Brightening',
            ],
            [
                'nama_jenis_treatment' => 'Slimming',
            ],
        ];

        foreach ($treatmentTypes as $typeData) {
            JenisTreatment::create($typeData);
        }
    }
}
