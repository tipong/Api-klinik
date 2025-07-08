<?php

namespace Database\Seeders;

use App\Models\BookingTreatment;
use App\Models\DetailBookingTreatment;
use App\Models\User;
use App\Models\Dokter;
use App\Models\Beautician;
use App\Models\Treatment;
use App\Models\JenisTreatment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BookingTreatmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure the dependent seeders have run
        $this->runDependentSeeders();

        // Get some users, doctors and beauticians for seeding
        $users = User::where('role', 'pelanggan')->take(10)->get();
        if ($users->isEmpty()) {
            // Create some customer users if none exist
            $this->createCustomers();
            $users = User::where('role', 'pelanggan')->take(10)->get();
            
            if ($users->isEmpty()) {
                $this->command->info('Could not create customers, skipping BookingTreatment seeding');
                return;
            }
        }

        $dokters = Dokter::take(3)->get();
        if ($dokters->isEmpty()) {
            $this->command->info('No doctors found, make sure to run DokterSeeder first');
            return;
        }

        $beauticians = Beautician::take(3)->get();
        if ($beauticians->isEmpty()) {
            // Create some beauticians if none exist
            $this->createBeauticians();
            $beauticians = Beautician::take(3)->get();
            
            if ($beauticians->isEmpty()) {
                $this->command->info('Could not create beauticians, skipping BookingTreatment seeding');
                return;
            }
        }

        // Create some treatments if none exist
        $this->createTreatments();
        $treatments = Treatment::take(5)->get();
        if ($treatments->isEmpty()) {
            $this->command->info('No treatments found, skipping BookingTreatment seeding');
            return;
        }

        // Get available promos (optional)
        $promos = collect(); // Empty collection by default
        
        // Check if promo table exists and the model is available
        if (Schema::hasTable('tb_promo')) {
            try {
                // Try to get promos if the model exists
                if (class_exists('App\Models\Promo')) {
                    $promos = app('App\Models\Promo')::take(3)->get();
                }
            } catch (\Exception $e) {
                $this->command->info('Promo model not available: ' . $e->getMessage());
            }
        }

        // Status options
        $statuses = ['Verifikasi', 'Berhasil dibooking', 'Selesai', 'Dibatalkan'];

        // Generate 30 booking treatments over the last 3 months 
        $bookings = [];
        for ($i = 0; $i < 30; $i++) {
            $user = $users->random();
            $dokter = $dokters->random();
            $beautician = $beauticians->random();
            $randomDaysAgo = rand(1, 90); // Between 1 and 90 days ago

            $waktuTreatment = Carbon::now()->subDays($randomDaysAgo)->setTime(rand(9, 17), 0, 0);
            
            // For past treatments, mostly set as completed
            // For past treatments, mostly set as completed
            $status = $randomDaysAgo > 5 ? 'Selesai' : $statuses[array_rand($statuses)];
            
            // Calculate treatments and cost
            $selectedTreatments = $treatments->random(rand(1, 3)); // 1-3 treatments per booking
            $hargaTotal = $selectedTreatments->sum('biaya_treatment');
            
            // Apply random promo if available
            $id_promo = null;
            $potonganHarga = 0;
            
            if ($promos->isNotEmpty() && rand(0, 1) == 1) { // 50% chance to have a promo
                $promo = $promos->random();
                $id_promo = $promo->id_promo;
                $potonganHarga = $hargaTotal * (rand(5, 20) / 100); // 5-20% discount
            }
            
            // Apply tax (11%)
            $besaranPajak = ($hargaTotal - $potonganHarga) * 0.11;
            
            // Calculate final price
            $hargaAkhir = $hargaTotal - $potonganHarga + $besaranPajak;
            
            // Create booking treatment
            $booking = BookingTreatment::create([
                'id_user' => $user->id_user,
                'waktu_treatment' => $waktuTreatment,
                'id_dokter' => $dokter->id_dokter,
                'id_beautician' => $beautician->id_beautician,
                'status_booking_treatment' => $status,
                'harga_total' => $hargaTotal,
                'id_promo' => $id_promo,
                'potongan_harga' => $potonganHarga,
                'besaran_pajak' => $besaranPajak,
                'harga_akhir_treatment' => $hargaAkhir,
            ]);
            
            // Create detail booking treatments
            foreach ($selectedTreatments as $treatment) {
                DetailBookingTreatment::create([
                    'id_booking_treatment' => $booking->id_booking_treatment,
                    'id_treatment' => $treatment->id_treatment,
                    'biaya_treatment' => $treatment->biaya_treatment,
                ]);
            }
            
            $bookings[] = $booking;
        }
        
        $this->command->info('Successfully seeded ' . count($bookings) . ' booking treatments');
    }
    
    /**
     * Run any dependent seeders that might be needed
     */
    private function runDependentSeeders()
    {
        // Check if DokterSeeder is already called in DatabaseSeeder
        // If not, run it here to ensure doctors exist
        if (Dokter::count() == 0) {
            $this->call(DokterSeeder::class);
        }
    }
    
    /**
     * Create customers if none exist
     */
    private function createCustomers()
    {
        $this->command->info('Creating customer accounts for booking treatments...');
        
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'nama_user' => 'Customer ' . $i,
                'no_telp' => '08987654' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email' => 'customer' . $i . '@example.com',
                'tanggal_lahir' => Carbon::now()->subYears(20 + $i)->format('Y-m-d'),
                'password' => bcrypt('password123'),
                'role' => 'pelanggan',
            ]);
        }
    }
    
    /**
     * Create beauticians if none exist
     */
    private function createBeauticians()
    {
        $this->command->info('Creating beautician data...');
        
        // First check if we have pegawai with right position
        $posisiBeautician = \App\Models\Posisi::where('nama_posisi', 'Beautician')
            ->first();
            
        if (!$posisiBeautician) {
            $posisiBeautician = \App\Models\Posisi::create([
                'nama_posisi' => 'Beautician',
                'gaji_pokok' => 6000000.00,
                'persen_bonus' => 7.50,
            ]);
        }
        
        // Create 3 beautician users, pegawai and beautician records
        $beauticianNames = [
            'Siti Nuraini',
            'Putri Handayani',
            'Maya Lestari'
        ];
        
        foreach ($beauticianNames as $index => $name) {
            // Create user
            $user = User::create([
                'nama_user' => $name,
                'no_telp' => '08765432' . str_pad($index + 100, 3, '0', STR_PAD_LEFT),
                'email' => 'beautician' . ($index + 1) . '@klinik.com',
                'tanggal_lahir' => Carbon::now()->subYears(25 + $index)->format('Y-m-d'),
                'password' => bcrypt('password123'),
                'role' => 'beautician',
            ]);
            
            // Create pegawai
            $pegawai = \App\Models\Pegawai::create([
                'id_user' => $user->id_user,
                'nama_lengkap' => $name,
                'tanggal_lahir' => $user->tanggal_lahir,
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Kecantikan No. ' . ($index + 1) . ', Jakarta',
                'telepon' => $user->no_telp,
                'email' => $user->email,
                'NIP' => 'BTN00' . ($index + 1),
                'NIK' => '99' . str_pad($index + 1, 14, '0', STR_PAD_LEFT),
                'id_posisi' => $posisiBeautician->id_posisi,
                'agama' => 'Islam',
                'tanggal_masuk' => Carbon::now()->subMonths($index + 3)->format('Y-m-d'),
            ]);
            
            // Create beautician
            Beautician::create([
                'id_pegawai' => $pegawai->id_pegawai,
                'nama_beautician' => $name,
                'no_telp' => $user->no_telp,
                'email_beautician' => $user->email,
                'NIP' => $pegawai->NIP,
            ]);
        }
    }
    
    /**
     * Create treatments if none exist
     */
    private function createTreatments()
    {
        // Only create if less than 5 treatments exist
        if (Treatment::count() >= 5) {
            return;
        }
        
        $this->command->info('Creating treatment data...');
        
        // Create jenis treatment if none exists
        $jenisTreatments = [
            'Facial',
            'Body Treatment',
            'Hair Treatment',
            'Skin Care',
            'Massage'
        ];
        
        $jenisIds = [];
        foreach ($jenisTreatments as $jenis) {
            $existingJenis = JenisTreatment::where('nama_jenis_treatment', $jenis)->first();
            if ($existingJenis) {
                $jenisIds[] = $existingJenis->id_jenis_treatment;
            } else {
                $newJenis = JenisTreatment::create([
                    'nama_jenis_treatment' => $jenis
                ]);
                $jenisIds[] = $newJenis->id_jenis_treatment;
            }
        }
        
        // Create treatments
        $treatments = [
            [
                'nama' => 'Deep Cleansing Facial',
                'deskripsi' => 'Pembersihan wajah mendalam untuk mengatasi masalah jerawat dan komedo.',
                'biaya' => 350000,
                'estimasi' => '01:00:00',
                'jenis_id' => $jenisIds[0] // Facial
            ],
            [
                'nama' => 'Body Scrub & Massage',
                'deskripsi' => 'Perawatan scrub seluruh tubuh diikuti dengan pijat relaksasi.',
                'biaya' => 500000,
                'estimasi' => '01:30:00',
                'jenis_id' => $jenisIds[1] // Body Treatment
            ],
            [
                'nama' => 'Hair Spa',
                'deskripsi' => 'Perawatan spa rambut untuk mengatasi kerusakan dan mengembalikan kilau alami.',
                'biaya' => 400000,
                'estimasi' => '01:15:00',
                'jenis_id' => $jenisIds[2] // Hair Treatment
            ],
            [
                'nama' => 'Anti Aging Facial',
                'deskripsi' => 'Perawatan khusus untuk mengurangi tanda-tanda penuaan pada wajah.',
                'biaya' => 750000,
                'estimasi' => '01:30:00',
                'jenis_id' => $jenisIds[0] // Facial
            ],
            [
                'nama' => 'Full Body Relaxation Massage',
                'deskripsi' => 'Pijat seluruh tubuh untuk relaksasi dan menghilangkan ketegangan otot.',
                'biaya' => 600000,
                'estimasi' => '02:00:00',
                'jenis_id' => $jenisIds[4] // Massage
            ],
            [
                'nama' => 'Acne Treatment',
                'deskripsi' => 'Perawatan khusus untuk kulit berjerawat dan berminyak.',
                'biaya' => 450000,
                'estimasi' => '01:15:00',
                'jenis_id' => $jenisIds[3] // Skin Care
            ],
            [
                'nama' => 'Brightening Treatment',
                'deskripsi' => 'Perawatan untuk mencerahkan dan meratakan warna kulit.',
                'biaya' => 550000,
                'estimasi' => '01:15:00',
                'jenis_id' => $jenisIds[3] // Skin Care
            ],
        ];
        
        foreach ($treatments as $treatment) {
            Treatment::create([
                'id_jenis_treatment' => $treatment['jenis_id'],
                'nama_treatment' => $treatment['nama'],
                'deskripsi_treatment' => $treatment['deskripsi'],
                'biaya_treatment' => $treatment['biaya'],
                'estimasi_treatment' => $treatment['estimasi'],
                'gambar_treatment' => 'default_treatment.jpg',
            ]);
        }
    }
}
