# Copilot Instructions untuk Klinik API

<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

## Project Context
Ini adalah project API Laravel untuk sistem manajemen klinik dengan fitur:
- Dashboard dengan statistik dan laporan
- Sistem absensi dengan GPS location tracking
- Manajemen rekrutmen dan aplikasi pekerjaan  
- Sistem pelatihan untuk staff
- Penggajian otomatis dengan perhitungan bonus dan absensi
- Manajemen pegawai dan users

## Role-based Access Control
System memiliki 7 role dengan akses berbeda:
- **admin**: Akses penuh semua fitur (tidak perlu absen)
- **hrd**: Akses penuh + wajib absen
- **beautician**: Absen, lihat pelatihan, lihat gaji
- **dokter**: Absen, lihat pelatihan, lihat gaji  
- **front_office**: Absen, lihat pelatihan, lihat gaji
- **kasir**: Absen, lihat pelatihan, lihat gaji
- **pelanggan**: Daftar lowongan kerja

## API Guidelines
- Semua response menggunakan format JSON consistent
- Implementasi proper error handling dengan status codes
- Authentication menggunakan Laravel Sanctum
- Gunakan Resource classes untuk format response
- Implementasi middleware untuk role-based authorization
- Input validation yang ketat
- Logging untuk audit trail

## GPS & Location Features
- Validasi radius kantor untuk absensi (default 100 meter)
- Store latitude, longitude, dan alamat untuk check-in/check-out
- Calculate distance menggunakan Haversine formula

## Salary Calculation
Gaji otomatis dihitung berdasarkan:
- Gaji pokok (basic salary)
- Bonus berdasarkan performa
- Tunjangan absensi (attendance allowance)
- Potongan keterlambatan
- Lembur (overtime)

## Database Schema
Gunakan proper foreign keys dan indexes untuk:
- users (dengan roles)
- attendances (dengan GPS data)
- recruitments & applications
- trainings & participants
- salaries & payments
- employees (pegawai)

## Code Style
- Gunakan bahasa Indonesia untuk komentar dan variable names yang user-facing
- Follow Laravel conventions dan PSR standards
- Implement Repository pattern untuk complex queries
- Use Form Requests for validation
- Create Service classes untuk business logic
