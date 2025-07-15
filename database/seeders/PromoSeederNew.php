<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promo;
use Carbon\Carbon;

class PromoSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promos = [
            [
                'nama_promo' => 'Grand Opening 50%',
                'jenis_promo' => 'Treatment',
                'deskripsi_promo' => 'Diskon 50% untuk semua treatment facial dalam rangka grand opening klinik',
                'tipe_potongan' => 'Diskon',
                'potongan_harga' => 50.00,
                'minimal_belanja' => 200000.00,
                'tanggal_mulai' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'tanggal_berakhir' => Carbon::now()->addDays(20)->format('Y-m-d'),
                'gambar_promo' => 'promo/grand_opening.jpg',
                'status_promo' => 'Aktif',
            ],
            [
                'nama_promo' => 'Birthday Special',
                'jenis_promo' => 'Treatment',
                'deskripsi_promo' => 'Potongan harga Rp 100.000 khusus untuk pelanggan yang berulang tahun bulan ini',
                'tipe_potongan' => 'Rupiah',
                'potongan_harga' => 100000.00,
                'minimal_belanja' => 300000.00,
                'tanggal_mulai' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                'tanggal_berakhir' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                'gambar_promo' => 'promo/birthday_special.jpg',
                'status_promo' => 'Aktif',
            ],
            [
                'nama_promo' => 'Weekend Treat',
                'jenis_promo' => 'Treatment',
                'deskripsi_promo' => 'Diskon 25% untuk treatment body massage setiap weekend',
                'tipe_potongan' => 'Diskon',
                'potongan_harga' => 25.00,
                'minimal_belanja' => 250000.00,
                'tanggal_mulai' => Carbon::now()->format('Y-m-d'),
                'tanggal_berakhir' => Carbon::now()->addMonths(3)->format('Y-m-d'),
                'gambar_promo' => 'promo/weekend_treat.jpg',
                'status_promo' => 'Aktif',
            ],
            [
                'nama_promo' => 'Student Discount',
                'jenis_promo' => 'Treatment',
                'deskripsi_promo' => 'Diskon 20% untuk pelajar dan mahasiswa dengan menunjukkan kartu pelajar',
                'tipe_potongan' => 'Diskon',
                'potongan_harga' => 20.00,
                'minimal_belanja' => 150000.00,
                'tanggal_mulai' => Carbon::now()->subDays(30)->format('Y-m-d'),
                'tanggal_berakhir' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                'gambar_promo' => 'promo/student_discount.jpg',
                'status_promo' => 'Aktif',
            ],
            [
                'nama_promo' => 'New Year Special',
                'jenis_promo' => 'Treatment',
                'deskripsi_promo' => 'Promo tahun baru dengan potongan hingga Rp 500.000',
                'tipe_potongan' => 'Rupiah',
                'potongan_harga' => 500000.00,
                'minimal_belanja' => 2000000.00,
                'tanggal_mulai' => '2025-01-01',
                'tanggal_berakhir' => '2025-01-31',
                'gambar_promo' => 'promo/new_year_special.jpg',
                'status_promo' => 'Tidak Aktif',
            ],
        ];

        foreach ($promos as $promoData) {
            Promo::create($promoData);
        }
    }
}
