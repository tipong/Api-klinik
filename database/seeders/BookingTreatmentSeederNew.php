<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookingTreatment;
use Carbon\Carbon;

class BookingTreatmentSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = [
            [
                'id_user' => 9, // Budi Pelanggan
                'waktu_treatment' => Carbon::now()->addDays(1)->setTime(10, 0)->format('Y-m-d H:i:s'),
                'id_dokter' => 1, // Dr. Ahmad
                'id_beautician' => null,
                'status_booking_treatment' => 'Berhasil dibooking',
                'harga_total' => 350000.00,
                'id_promo' => 1, // Grand Opening 50%
                'potongan_harga' => 175000.00,
                'besaran_pajak' => 17500.00,
                'harga_akhir_treatment' => 192500.00,
            ],
            [
                'id_user' => 10, // Sinta Pelanggan
                'waktu_treatment' => Carbon::now()->addDays(2)->setTime(14, 30)->format('Y-m-d H:i:s'),
                'id_dokter' => null,
                'id_beautician' => 1, // Maria
                'status_booking_treatment' => 'Verifikasi',
                'harga_total' => 250000.00,
                'id_promo' => null,
                'potongan_harga' => 0.00,
                'besaran_pajak' => 25000.00,
                'harga_akhir_treatment' => 275000.00,
            ],
            [
                'id_user' => 9, // Budi Pelanggan
                'waktu_treatment' => Carbon::now()->subDays(5)->setTime(11, 0)->format('Y-m-d H:i:s'),
                'id_dokter' => 2, // Dr. Sari
                'id_beautician' => null,
                'status_booking_treatment' => 'Selesai',
                'harga_total' => 2500000.00,
                'id_promo' => null,
                'potongan_harga' => 0.00,
                'besaran_pajak' => 250000.00,
                'harga_akhir_treatment' => 2750000.00,
            ],
            [
                'id_user' => 10, // Sinta Pelanggan
                'waktu_treatment' => Carbon::now()->subDays(3)->setTime(9, 30)->format('Y-m-d H:i:s'),
                'id_dokter' => null,
                'id_beautician' => 2, // Linda
                'status_booking_treatment' => 'Selesai',
                'harga_total' => 300000.00,
                'id_promo' => 3, // Weekend Treat
                'potongan_harga' => 75000.00,
                'besaran_pajak' => 22500.00,
                'harga_akhir_treatment' => 247500.00,
            ],
            [
                'id_user' => 9, // Budi Pelanggan
                'waktu_treatment' => Carbon::now()->addDays(7)->setTime(13, 0)->format('Y-m-d H:i:s'),
                'id_dokter' => 1, // Dr. Ahmad
                'id_beautician' => null,
                'status_booking_treatment' => 'Berhasil dibooking',
                'harga_total' => 400000.00,
                'id_promo' => 2, // Birthday Special
                'potongan_harga' => 100000.00,
                'besaran_pajak' => 30000.00,
                'harga_akhir_treatment' => 330000.00,
            ],
            [
                'id_user' => 10, // Sinta Pelanggan
                'waktu_treatment' => Carbon::now()->addDays(3)->setTime(15, 30)->format('Y-m-d H:i:s'),
                'id_dokter' => null,
                'id_beautician' => 1, // Maria
                'status_booking_treatment' => 'Verifikasi',
                'harga_total' => 180000.00,
                'id_promo' => 4, // Student Discount
                'potongan_harga' => 36000.00,
                'besaran_pajak' => 14400.00,
                'harga_akhir_treatment' => 158400.00,
            ],
        ];

        foreach ($bookings as $bookingData) {
            BookingTreatment::create($bookingData);
        }
    }
}
