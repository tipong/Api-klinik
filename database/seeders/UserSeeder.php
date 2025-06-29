<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'Admin Klinik',
            'email' => 'admin@klinik.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1, Jakarta',
            'is_active' => true,
        ]);

        // HRD user
        $hrd = User::create([
            'name' => 'HRD Manager',
            'email' => 'hrd@klinik.com',
            'password' => Hash::make('password123'),
            'role' => 'hrd',
            'phone' => '081234567891',
            'address' => 'Jl. HRD No. 2, Jakarta',
            'is_active' => true,
        ]);

        // Create employee record for HRD
        Employee::create([
            'user_id' => $hrd->id,
            'employee_id' => 'EMP00001',
            'nip' => 'NIP001',
            'departemen' => 'Human Resources',
            'jabatan' => 'HRD Manager',
            'tanggal_bergabung' => now()->subYears(2),
            'gaji_pokok' => 8000000,
            'tunjangan_tetap' => 1500000,
            'status_kerja' => 'tetap',
            'is_active' => true,
        ]);

        // Beautician user
        $beautician = User::create([
            'name' => 'Beautician Sari',
            'email' => 'beautician@klinik.com',
            'password' => Hash::make('password123'),
            'role' => 'beautician',
            'phone' => '081234567892',
            'address' => 'Jl. Beautician No. 3, Jakarta',
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => $beautician->id,
            'employee_id' => 'EMP00002',
            'nip' => 'NIP002',
            'departemen' => 'Beauty Treatment',
            'jabatan' => 'Senior Beautician',
            'tanggal_bergabung' => now()->subYears(1),
            'gaji_pokok' => 5000000,
            'tunjangan_tetap' => 800000,
            'status_kerja' => 'tetap',
            'is_active' => true,
        ]);

        // Dokter user
        $dokter = User::create([
            'name' => 'Dr. Ahmad',
            'email' => 'dokter@klinik.com',
            'password' => Hash::make('password123'),
            'role' => 'dokter',
            'phone' => '081234567893',
            'address' => 'Jl. Dokter No. 4, Jakarta',
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => $dokter->id,
            'employee_id' => 'EMP00003',
            'nip' => 'NIP003',
            'departemen' => 'Medical',
            'jabatan' => 'Dokter Umum',
            'tanggal_bergabung' => now()->subMonths(6),
            'gaji_pokok' => 12000000,
            'tunjangan_tetap' => 2000000,
            'status_kerja' => 'tetap',
            'is_active' => true,
        ]);

        // Front Office user
        $frontOffice = User::create([
            'name' => 'Front Office Rina',
            'email' => 'frontoffice@klinik.com',
            'password' => Hash::make('password123'),
            'role' => 'front_office',
            'phone' => '081234567894',
            'address' => 'Jl. Front Office No. 5, Jakarta',
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => $frontOffice->id,
            'employee_id' => 'EMP00004',
            'nip' => 'NIP004',
            'departemen' => 'Customer Service',
            'jabatan' => 'Front Office',
            'tanggal_bergabung' => now()->subMonths(3),
            'gaji_pokok' => 4000000,
            'tunjangan_tetap' => 600000,
            'status_kerja' => 'kontrak',
            'kontrak_mulai' => now()->subMonths(3),
            'kontrak_selesai' => now()->addMonths(9),
            'is_active' => true,
        ]);

        // Kasir user
        $kasir = User::create([
            'name' => 'Kasir Budi',
            'email' => 'kasir@klinik.com',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
            'phone' => '081234567895',
            'address' => 'Jl. Kasir No. 6, Jakarta',
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => $kasir->id,
            'employee_id' => 'EMP00005',
            'nip' => 'NIP005',
            'departemen' => 'Finance',
            'jabatan' => 'Kasir',
            'tanggal_bergabung' => now()->subMonths(4),
            'gaji_pokok' => 3500000,
            'tunjangan_tetap' => 500000,
            'status_kerja' => 'kontrak',
            'kontrak_mulai' => now()->subMonths(4),
            'kontrak_selesai' => now()->addMonths(8),
            'is_active' => true,
        ]);

        // Pelanggan user (for job applications)
        User::create([
            'name' => 'Pelanggan Test',
            'email' => 'pelanggan@test.com',
            'password' => Hash::make('password123'),
            'role' => 'pelanggan',
            'phone' => '081234567896',
            'address' => 'Jl. Pelanggan No. 7, Jakarta',
            'is_active' => true,
        ]);

        $this->command->info('Users and employees seeded successfully!');
    }
}
