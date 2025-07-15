<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Treatment;

class TreatmentSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $treatments = [
            // Facial Treatments
            [
                'id_jenis_treatment' => 1,
                'nama_treatment' => 'Basic Facial',
                'deskripsi_treatment' => 'Facial dasar untuk membersihkan wajah dengan deep cleansing, steam, dan masker',
                'biaya_treatment' => 150000.00,
                'estimasi_treatment' => '01:00:00',
                'gambar_treatment' => 'treatments/basic_facial.jpg',
            ],
            [
                'id_jenis_treatment' => 1,
                'nama_treatment' => 'Hydrating Facial',
                'deskripsi_treatment' => 'Facial untuk melembabkan kulit kering dengan serum hyaluronic acid',
                'biaya_treatment' => 250000.00,
                'estimasi_treatment' => '01:30:00',
                'gambar_treatment' => 'treatments/hydrating_facial.jpg',
            ],
            [
                'id_jenis_treatment' => 1,
                'nama_treatment' => 'Chemical Peeling',
                'deskripsi_treatment' => 'Perawatan eksfoliasi dengan bahan kimia untuk regenerasi kulit',
                'biaya_treatment' => 350000.00,
                'estimasi_treatment' => '01:15:00',
                'gambar_treatment' => 'treatments/chemical_peeling.jpg',
            ],

            // Body Treatments
            [
                'id_jenis_treatment' => 2,
                'nama_treatment' => 'Body Scrub',
                'deskripsi_treatment' => 'Lulur tubuh untuk mengangkat sel kulit mati dan melembutkan kulit',
                'biaya_treatment' => 200000.00,
                'estimasi_treatment' => '01:00:00',
                'gambar_treatment' => 'treatments/body_scrub.jpg',
            ],
            [
                'id_jenis_treatment' => 2,
                'nama_treatment' => 'Hot Stone Massage',
                'deskripsi_treatment' => 'Pijat relaksasi menggunakan batu panas untuk melancarkan peredaran darah',
                'biaya_treatment' => 300000.00,
                'estimasi_treatment' => '01:30:00',
                'gambar_treatment' => 'treatments/hot_stone_massage.jpg',
            ],

            // Anti Aging Treatments
            [
                'id_jenis_treatment' => 3,
                'nama_treatment' => 'Botox Treatment',
                'deskripsi_treatment' => 'Suntik botox untuk mengurangi kerutan dan garis halus di wajah',
                'biaya_treatment' => 2500000.00,
                'estimasi_treatment' => '00:45:00',
                'gambar_treatment' => 'treatments/botox.jpg',
            ],
            [
                'id_jenis_treatment' => 3,
                'nama_treatment' => 'Dermal Filler',
                'deskripsi_treatment' => 'Filler untuk mengisi volume wajah dan mengurangi kerutan dalam',
                'biaya_treatment' => 3000000.00,
                'estimasi_treatment' => '01:00:00',
                'gambar_treatment' => 'treatments/dermal_filler.jpg',
            ],

            // Acne Treatments
            [
                'id_jenis_treatment' => 4,
                'nama_treatment' => 'Acne Facial',
                'deskripsi_treatment' => 'Facial khusus untuk kulit berjerawat dengan ekstraksi komedo',
                'biaya_treatment' => 180000.00,
                'estimasi_treatment' => '01:15:00',
                'gambar_treatment' => 'treatments/acne_facial.jpg',
            ],
            [
                'id_jenis_treatment' => 4,
                'nama_treatment' => 'Laser Acne Treatment',
                'deskripsi_treatment' => 'Perawatan laser untuk mengatasi jerawat dan bekas jerawat',
                'biaya_treatment' => 800000.00,
                'estimasi_treatment' => '00:30:00',
                'gambar_treatment' => 'treatments/laser_acne.jpg',
            ],

            // Brightening Treatments
            [
                'id_jenis_treatment' => 5,
                'nama_treatment' => 'Vitamin C Infusion',
                'deskripsi_treatment' => 'Infus vitamin C untuk mencerahkan dan melindungi kulit dari radikal bebas',
                'biaya_treatment' => 400000.00,
                'estimasi_treatment' => '01:00:00',
                'gambar_treatment' => 'treatments/vitamin_c.jpg',
            ],
            [
                'id_jenis_treatment' => 5,
                'nama_treatment' => 'IPL Photofacial',
                'deskripsi_treatment' => 'Intense Pulsed Light untuk mencerahkan dan meratakan warna kulit',
                'biaya_treatment' => 1200000.00,
                'estimasi_treatment' => '00:45:00',
                'gambar_treatment' => 'treatments/ipl_photofacial.jpg',
            ],

            // Slimming Treatments
            [
                'id_jenis_treatment' => 6,
                'nama_treatment' => 'Cavitation Treatment',
                'deskripsi_treatment' => 'Perawatan ultrasonik untuk menghancurkan lemak dan mengencangkan kulit',
                'biaya_treatment' => 500000.00,
                'estimasi_treatment' => '01:00:00',
                'gambar_treatment' => 'treatments/cavitation.jpg',
            ],
            [
                'id_jenis_treatment' => 6,
                'nama_treatment' => 'Body Contouring',
                'deskripsi_treatment' => 'Perawatan pembentukan tubuh dengan teknologi RF dan vacuum',
                'biaya_treatment' => 750000.00,
                'estimasi_treatment' => '01:30:00',
                'gambar_treatment' => 'treatments/body_contouring.jpg',
            ],
        ];

        foreach ($treatments as $treatmentData) {
            Treatment::create($treatmentData);
        }
    }
}
