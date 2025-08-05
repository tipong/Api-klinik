<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PelatihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_pelatihan')->delete(); // opsional: kosongkan data dulu

        DB::table('tb_pelatihan')->insert([
            [
                'judul' => 'Pelatihan Laravel via Zoom',
                'deskripsi' => 'Pelatihan membangun aplikasi web menggunakan Laravel melalui Zoom.',
                'jenis_pelatihan' => 'zoom',
                'jadwal_pelatihan' => Carbon::create(2025, 8, 10, 10, 0, 0),
                'link_url' => 'https://zoom.us/j/1234567890',
                'durasi' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Pelatihan Soft Skill Offline',
                'deskripsi' => 'Pelatihan keterampilan komunikasi dan kepemimpinan secara langsung.',
                'jenis_pelatihan' => 'offline',
                'jadwal_pelatihan' => Carbon::create(2025, 8, 15, 9, 0, 0),
                'link_url' => null,
                'durasi' => 90,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Panduan Penggunaan Sistem Klinik',
                'deskripsi' => 'Dokumen PDF panduan penggunaan sistem informasi klinik.',
                'jenis_pelatihan' => 'document',
                'jadwal_pelatihan' => null, // NULL bukan waktu
                'link_url' => 'https://example.com/pelatihan/panduan.pdf',
                'durasi' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Video Pelatihan Keselamatan Kerja',
                'deskripsi' => 'Video pelatihan keselamatan kerja yang dapat diakses kapan saja.',
                'jenis_pelatihan' => 'video',
                'jadwal_pelatihan' => null, // NULL juga valid
                'link_url' => 'https://youtube.com/example-video',
                'durasi' => 45,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
