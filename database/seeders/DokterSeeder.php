<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dokter;
use App\Models\Pegawai;
use App\Models\User;

class DokterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing pegawai records with role dokter
        $pegawaiDokters = Pegawai::whereHas('user', function ($query) {
            $query->where('role', 'dokter');
        })->get();

        if ($pegawaiDokters->isEmpty()) {
            $this->command->info('No pegawai with role dokter found, creating sample data');
            
            // Create sample users with dokter role
            $dokterUsers = [];
            $dokterNames = [
                'Dr. Ahmad Zulkarnaen',
                'Dr. Siti Rahmawati',
                'Dr. Budi Santoso',
                'Dr. Dewi Lestari',
                'Dr. Fajar Nugroho'
            ];
            
            for ($i = 0; $i < count($dokterNames); $i++) {
                $user = User::create([
                    'nama_user' => $dokterNames[$i],
                    'no_telp' => '08123456' . str_pad($i + 1000, 4, '0', STR_PAD_LEFT),
                    'email' => 'dokter' . ($i + 1) . '@klinik.com',
                    'tanggal_lahir' => fake()->date('Y-m-d', '-30 years'),
                    'password' => bcrypt('password123'),
                    'role' => 'dokter'
                ]);
                
                $dokterUsers[] = $user;
            }
            
            // Get dokter position
            $posisiDokter = \App\Models\Posisi::where('nama_posisi', 'Dokter')->first();
            if (!$posisiDokter) {
                $posisiDokter = \App\Models\Posisi::create([
                    'nama_posisi' => 'Dokter',
                    'gaji_pokok' => 15000000.00,
                    'persen_bonus' => 10.00,
                ]);
            }
            
            // Create pegawai records for the dokter users
            foreach ($dokterUsers as $index => $user) {
                $pegawai = Pegawai::create([
                    'id_user' => $user->id_user,
                    'nama_lengkap' => $dokterNames[$index],
                    'tanggal_lahir' => $user->tanggal_lahir,
                    'jenis_kelamin' => fake()->randomElement(['Laki-laki', 'Perempuan']),
                    'alamat' => fake()->address,
                    'telepon' => $user->no_telp,
                    'email' => $user->email,
                    'NIP' => 'DOK' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'NIK' => fake()->numerify('################'),
                    'id_posisi' => $posisiDokter->id_posisi,
                    'agama' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
                    'tanggal_masuk' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
                ]);
                
                $pegawaiDokters[] = $pegawai;
            }
        }

        // Create dokter records for each pegawai with role dokter
        foreach ($pegawaiDokters as $pegawai) {
            // Check if doctor record already exists
            $dokterExists = Dokter::where('id_pegawai', $pegawai->id_pegawai)->exists();
            
            if (!$dokterExists) {
                Dokter::create([
                    'id_pegawai' => $pegawai->id_pegawai,
                    'nama_dokter' => $pegawai->nama_lengkap,
                    'no_telp' => $pegawai->telepon,
                    'email_dokter' => $pegawai->email,
                    'NIP' => $pegawai->NIP,
                    'foto_dokter' => 'default_doctor.jpg',
                ]);
            }
        }
        
        $this->command->info('Successfully created ' . Dokter::count() . ' doctor records.');
    }
}
