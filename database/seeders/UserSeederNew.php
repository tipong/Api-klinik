<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'nama_user' => 'Super Admin',
                'no_telp' => '081234567890',
                'email' => 'admin@klinik.com',
                'tanggal_lahir' => '1985-01-15',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'foto_profil' => 'admin.jpg',
            ],
            [
                'nama_user' => 'HRD Manager',
                'no_telp' => '081234567891',
                'email' => 'hrd@klinik.com',
                'tanggal_lahir' => '1987-03-22',
                'password' => Hash::make('hrd123'),
                'role' => 'hrd',
                'foto_profil' => 'hrd.jpg',
            ],
            [
                'nama_user' => 'Dr. Ahmad Zulkarnaen',
                'no_telp' => '081234567892',
                'email' => 'dokter1@klinik.com',
                'tanggal_lahir' => '1980-05-10',
                'password' => Hash::make('dokter123'),
                'role' => 'dokter',
                'foto_profil' => 'dokter1.jpg',
            ],
            [
                'nama_user' => 'Dr. Sari Handayani',
                'no_telp' => '081234567893',
                'email' => 'dokter2@klinik.com',
                'tanggal_lahir' => '1983-08-25',
                'password' => Hash::make('dokter123'),
                'role' => 'dokter',
                'foto_profil' => 'dokter2.jpg',
            ],
            [
                'nama_user' => 'Maria Beautician',
                'no_telp' => '081234567894',
                'email' => 'beautician1@klinik.com',
                'tanggal_lahir' => '1990-02-14',
                'password' => Hash::make('beautician123'),
                'role' => 'beautician',
                'foto_profil' => 'beautician1.jpg',
            ],
            [
                'nama_user' => 'Linda Beautician',
                'no_telp' => '081234567895',
                'email' => 'beautician2@klinik.com',
                'tanggal_lahir' => '1992-07-18',
                'password' => Hash::make('beautician123'),
                'role' => 'beautician',
                'foto_profil' => 'beautician2.jpg',
            ],
            [
                'nama_user' => 'Rina Front Office',
                'no_telp' => '081234567896',
                'email' => 'frontoffice@klinik.com',
                'tanggal_lahir' => '1995-04-12',
                'password' => Hash::make('front123'),
                'role' => 'front office',
                'foto_profil' => 'front1.jpg',
            ],
            [
                'nama_user' => 'Devi Kasir',
                'no_telp' => '081234567897',
                'email' => 'kasir@klinik.com',
                'tanggal_lahir' => '1993-09-30',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'foto_profil' => 'kasir1.jpg',
            ],
            [
                'nama_user' => 'Budi Pelanggan',
                'no_telp' => '081234567898',
                'email' => 'pelanggan1@gmail.com',
                'tanggal_lahir' => '1988-12-05',
                'password' => Hash::make('pelanggan123'),
                'role' => 'pelanggan',
                'foto_profil' => 'pelanggan1.jpg',
            ],
            [
                'nama_user' => 'Sinta Pelanggan',
                'no_telp' => '081234567899',
                'email' => 'pelanggan2@gmail.com',
                'tanggal_lahir' => '1991-06-20',
                'password' => Hash::make('pelanggan123'),
                'role' => 'pelanggan',
                'foto_profil' => 'pelanggan2.jpg',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
