<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LowonganPekerjaan;
use Carbon\Carbon;

class LowonganPekerjaanSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobPostings = [
            [
                'id_posisi' => 6, // Front Office
                'judul_pekerjaan' => 'Front Office Staff - Klinik Kecantikan',
                'jumlah_lowongan' => 2,
                'pengalaman_minimal' => '1 tahun',
                'gaji_minimal' => 4500000.00,
                'gaji_maksimal' => 6000000.00,
                'status' => 'aktif',
                'tanggal_mulai' => Carbon::now()->format('Y-m-d'),
                'tanggal_selesai' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'deskripsi' => 'Kami mencari Front Office Staff yang ramah dan berpengalaman untuk melayani pelanggan di klinik kecantikan terkemuka. Kandidat harus memiliki kemampuan komunikasi yang baik dan dapat bekerja dalam tim.',
                'persyaratan' => "- Pendidikan minimal SMA/SMK\n- Pengalaman minimal 1 tahun di bidang customer service\n- Berpenampilan menarik dan rapi\n- Memiliki kemampuan komunikasi yang baik\n- Dapat mengoperasikan komputer dan sistem POS\n- Bersedia bekerja shift",
            ],
            [
                'id_posisi' => 4, // Beautician Junior
                'judul_pekerjaan' => 'Beautician Junior - Perawatan Wajah',
                'jumlah_lowongan' => 3,
                'pengalaman_minimal' => '0-1 tahun',
                'gaji_minimal' => 5500000.00,
                'gaji_maksimal' => 7000000.00,
                'status' => 'aktif',
                'tanggal_mulai' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'tanggal_selesai' => Carbon::now()->addDays(25)->format('Y-m-d'),
                'deskripsi' => 'Bergabunglah dengan tim beautician profesional kami! Kami mencari beautician junior yang passionate dalam dunia kecantikan dan perawatan kulit. Training akan diberikan untuk fresh graduate.',
                'persyaratan' => "- Pendidikan minimal SMK Kecantikan atau D3 Tata Rias\n- Fresh graduate atau pengalaman 0-1 tahun\n- Memiliki sertifikat perawatan wajah/beauty therapy\n- Berpenampilan menarik dan hygiene yang baik\n- Teliti dan sabar dalam bekerja\n- Bersedia mengikuti training",
            ],
            [
                'id_posisi' => 7, // Kasir
                'judul_pekerjaan' => 'Kasir - Part Time/Full Time',
                'jumlah_lowongan' => 1,
                'pengalaman_minimal' => '6 bulan',
                'gaji_minimal' => 4000000.00,
                'gaji_maksimal' => 5000000.00,
                'status' => 'aktif',
                'tanggal_mulai' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'tanggal_selesai' => Carbon::now()->addDays(20)->format('Y-m-d'),
                'deskripsi' => 'Dibutuhkan kasir yang jujur dan teliti untuk menangani transaksi pembayaran di klinik. Tersedia posisi part time dan full time dengan benefit menarik.',
                'persyaratan' => "- Pendidikan minimal SMA\n- Pengalaman minimal 6 bulan sebagai kasir\n- Jujur dan dapat dipercaya\n- Teliti dalam menghitung uang\n- Dapat mengoperasikan mesin kasir dan EDC\n- Bersedia bekerja part time atau full time",
            ],
            [
                'id_posisi' => 8, // Cleaning Service
                'judul_pekerjaan' => 'Cleaning Service - Kebersihan Klinik',
                'jumlah_lowongan' => 2,
                'pengalaman_minimal' => 'Tidak ada',
                'gaji_minimal' => 3200000.00,
                'gaji_maksimal' => 4000000.00,
                'status' => 'aktif',
                'tanggal_mulai' => Carbon::now()->format('Y-m-d'),
                'tanggal_selesai' => Carbon::now()->addDays(15)->format('Y-m-d'),
                'deskripsi' => 'Kami membutuhkan cleaning service yang bertanggung jawab untuk menjaga kebersihan dan sterilitas klinik. Pekerjaan mencakup pembersihan ruangan, alat-alat medis, dan area umum.',
                'persyaratan' => "- Pendidikan minimal SD/SMP\n- Sehat jasmani dan rohani\n- Rajin dan bertanggung jawab\n- Memahami prosedur kebersihan medis (akan dilatih)\n- Dapat bekerja dalam tim\n- Bersedia bekerja shift pagi/sore",
            ],
            [
                'id_posisi' => 2, // Dokter Umum
                'judul_pekerjaan' => 'Dokter Umum - Klinik Kecantikan',
                'jumlah_lowongan' => 1,
                'pengalaman_minimal' => '2 tahun',
                'gaji_minimal' => 12000000.00,
                'gaji_maksimal' => 18000000.00,
                'status' => 'nonaktif',
                'tanggal_mulai' => Carbon::now()->subDays(45)->format('Y-m-d'),
                'tanggal_selesai' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'deskripsi' => 'Dibutuhkan dokter umum untuk menangani konsultasi medis dan prosedur kecantikan non-invasif. Posisi ini sudah terisi.',
                'persyaratan' => "- Dokter umum dengan STR aktif\n- Pengalaman minimal 2 tahun\n- Memiliki minat di bidang dermatologi/estetika\n- Kemampuan komunikasi yang baik\n- Bersedia mengikuti training prosedur kecantikan",
            ],
        ];

        foreach ($jobPostings as $jobData) {
            LowonganPekerjaan::create($jobData);
        }
    }
}
