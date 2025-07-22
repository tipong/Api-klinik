<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core HR System Tables (in order of dependencies)
            UserSeederNew::class,
            PosisiSeederNew::class,
            PegawaiSeederNew::class,
            
            // Beauty Clinic Core Tables
            DokterSeederNew::class,
            BeauticianSeederNew::class,
            JenisTreatmentSeederNew::class,
            TreatmentSeederNew::class,
            PromoSeederNew::class,
            
            // Booking System (requires doctors and beauticians)
            BookingTreatmentSeederNew::class,
            
            // Attendance System (requires pegawai)
            AbsensiSeeder::class,
            
            // Training System
            PelatihanSeeder::class,
        ]);
    }
}
