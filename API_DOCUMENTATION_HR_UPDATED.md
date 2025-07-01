# HR API Documentation - Updated Schema

This document describes the HR-related API endpoints for the Klinik Aesthetic system, updated to match the new database schema from `klinik-HR-2.sql`.

## Base URL
```
http://localhost:8000/api
```

## Authentication
All endpoints require authentication using Bearer token (Laravel Sanctum).

```
Authorization: Bearer {token}
```

## Table Overview

The following tables are used in the HR API:
- `tb_user` - User accounts and authentication
- `tb_pegawai` - Employee data
- `tb_posisi` - Job positions with salary information
- `tb_absensi` - Employee attendance (simplified - only date, no time)
- `tb_gaji` - Employee salaries with automatic calculation
- `tb_lowongan_pekerjaan` - Job openings
- `tb_lamaran_pekerjaan` - Job applications
- `tb_wawancara` - Interview schedules
- `tb_hasil_seleksi` - Selection results
- `tb_pelatihan` - Training programs

## 1. User Management

### GET /api/users
Get list of users with pagination and filtering.

**Query Parameters:**
- `role` - Filter by role (pelanggan, dokter, beautician, front office, kasir, admin)
- `search` - Search by name, email, or phone

**Response:**
```json
{
    "status": "success",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id_user": 1,
                "nama_user": "John Doe",
                "email": "john@example.com",
                "no_telp": "081234567890",
                "tanggal_lahir": "1990-01-01",
                "foto_profil": null,
                "role": "admin",
                "created_at": "2025-07-01T00:00:00.000000Z"
            }
        ],
        "total": 1
    }
}
```

### POST /api/users
Create a new user.

**Request Body:**
```json
{
    "nama_user": "Jane Doe",
    "email": "jane@example.com",
    "no_telp": "081234567891",
    "tanggal_lahir": "1992-05-15",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "front office"
}
```

## 2. Employee Management

### GET /api/pegawai
Get list of employees with relationships.

**Query Parameters:**
- `status` - Filter by status (active, inactive)
- `id_posisi` - Filter by position
- `search` - Search by name

**Response:**
```json
{
    "status": "success",
    "data": {
        "data": [
            {
                "id_pegawai": 1,
                "nama_lengkap": "John Doe",
                "NIP": "NIP001",
                "NIK": "1234567890123456",
                "tanggal_masuk": "2024-01-01",
                "tanggal_keluar": null,
                "user": {
                    "id_user": 1,
                    "nama_user": "John Doe",
                    "email": "john@example.com"
                },
                "posisi": {
                    "id_posisi": 1,
                    "nama_posisi": "Manager",
                    "gaji_pokok": "5000000.00"
                }
            }
        ]
    }
}
```

### POST /api/pegawai
Create a new employee.

**Request Body:**
```json
{
    "nama_lengkap": "Jane Smith",
    "tanggal_lahir": "1990-03-15",
    "jenis_kelamin": "Perempuan",
    "alamat": "Jl. Example No. 123",
    "telepon": "081234567890",
    "email": "jane.smith@example.com",
    "NIP": "NIP002",
    "NIK": "1234567890123457",
    "id_posisi": 2,
    "agama": "Islam",
    "tanggal_masuk": "2025-07-01",
    "create_user": true,
    "password": "password123",
    "password_confirmation": "password123",
    "role": "kasir"
}
```

## 3. Position Management

### GET /api/posisi
Get list of positions.

**Query Parameters:**
- `search` - Search by position name

### GET /api/posisi/statistics
Get position statistics including employee count and salary information.

**Response:**
```json
{
    "status": "success",
    "data": {
        "summary": {
            "total_posisi": 5,
            "total_pegawai_aktif": 25,
            "total_gaji_pokok_bulanan": 125000000,
            "rata_rata_gaji_pokok": 5000000,
            "gaji_pokok_tertinggi": 10000000,
            "gaji_pokok_terendah": 3000000
        },
        "by_posisi": [
            {
                "id_posisi": 1,
                "nama_posisi": "Manager",
                "gaji_pokok": 10000000,
                "persen_bonus": 15.00,
                "jumlah_pegawai_aktif": 3,
                "total_gaji_pokok_bulanan": 30000000
            }
        ]
    }
}
```

## 4. Attendance Management

### GET /api/absensi
Get attendance records.

**Query Parameters:**
- `id_pegawai` - Filter by employee
- `tanggal_dari` - Start date filter
- `tanggal_sampai` - End date filter
- `bulan` - Filter by month
- `tahun` - Filter by year

### POST /api/absensi
Record attendance (simplified - only date).

**Request Body:**
```json
{
    "id_pegawai": 1,
    "tanggal": "2025-07-01"
}
```

### GET /api/absensi/report
Get attendance report with statistics.

**Query Parameters:**
- `bulan` - Month (1-12)
- `tahun` - Year
- `id_pegawai` - Specific employee (optional)

## 5. Salary Management

### GET /api/gaji
Get salary records with filtering and pagination.

**Query Parameters:**
- `tahun` - Filter by year
- `bulan` - Filter by month  
- `status` - Filter by payment status (Terbayar, Belum Terbayar)
- `id_pegawai` - Filter by employee (admin/HR only)

### POST /api/gaji/generate
**[NEW]** Automatically calculate and generate salaries for a period.

**Request Body:**
```json
{
    "periode_bulan": 7,
    "periode_tahun": 2025
}
```

**Calculation Logic:**
- `gaji_pokok`: From employee's position
- `gaji_bonus`: percentage of gaji_pokok based on position's persen_bonus
- `gaji_kehadiran`: attendance-based bonus (10% of gaji_pokok proportional to attendance)
- `gaji_total`: sum of all components

### GET /api/gaji/preview
**[NEW]** Preview salary calculation without saving.

**Query Parameters:**
- `periode_bulan` - Month (1-12)
- `periode_tahun` - Year  
- `id_pegawai` - Specific employee (optional)

**Response:**
```json
{
    "status": "success",
    "data": {
        "periode": {
            "bulan": 7,
            "tahun": 2025,
            "periode_formatted": "July 2025"
        },
        "summary": {
            "total_pegawai": 10,
            "total_gaji_keseluruhan": 75500000,
            "rata_rata_gaji": 7550000
        },
        "calculations": [
            {
                "pegawai": {
                    "id_pegawai": 1,
                    "nama_lengkap": "John Doe",
                    "nip": "NIP001",
                    "posisi": "Manager"
                },
                "kehadiran": {
                    "total_hari_kerja": 22,
                    "hadir": 20,
                    "persentase_kehadiran": 90.91
                },
                "gaji": {
                    "gaji_pokok": 5000000,
                    "gaji_bonus": 750000,
                    "gaji_kehadiran": 454545,
                    "gaji_total": 6204545,
                    "bonus_percentage": 15
                },
                "already_exists": false
            }
        ]
    }
}
```

### GET /api/gaji/statistics
**[NEW]** Get salary statistics for a period.

**Query Parameters:**
- `periode_bulan` - Month (optional)
- `periode_tahun` - Year (optional, defaults to current year)

## 6. Job Openings Management

### GET /api/lowongan-pekerjaan
Get job openings with filtering.

**Query Parameters:**
- `status` - Filter by status (aktif, nonaktif)
- `id_posisi` - Filter by position
- `search` - Search by title
- `active` - Show only active openings

### POST /api/lowongan-pekerjaan
Create a new job opening.

**Request Body:**
```json
{
    "judul_pekerjaan": "Frontend Developer",
    "id_posisi": 3,
    "jumlah_lowongan": 2,
    "pengalaman_minimal": "2 tahun",
    "gaji_minimal": 4000000,
    "gaji_maksimal": 6000000,
    "status": "aktif",
    "tanggal_mulai": "2025-07-01",
    "tanggal_selesai": "2025-08-01",
    "deskripsi": "Lowongan untuk Frontend Developer",
    "persyaratan": "- Minimal S1\n- Pengalaman 2 tahun"
}
```

## 7. Job Applications Management

### GET /api/lamaran-pekerjaan
Get job applications.

**Query Parameters:**
- `id_lowongan_pekerjaan` - Filter by job opening
- `status_lamaran` - Filter by status (pending, diterima, ditolak)
- `search` - Search by applicant name

### POST /api/lamaran-pekerjaan
Create a new job application.

**Request Body:**
```json
{
    "id_lowongan_pekerjaan": 1,
    "nama_pelamar": "Alice Johnson",
    "email_pelamar": "alice@example.com",
    "NIK_pelamar": "1234567890123458",
    "telepon_pelamar": "081234567892",
    "alamat_pelamar": "Jl. Contoh No. 456",
    "pendidikan_terakhir": "S1 Teknik Informatika",
    "CV": "base64_encoded_file"
}
```

## 8. Interview Management

### GET /api/wawancara
Get interview schedules.

**Query Parameters:**
- `id_lamaran_pekerjaan` - Filter by application
- `tanggal_dari` - Start date filter
- `tanggal_sampai` - End date filter
- `hasil` - Filter by result (pending, diterima, ditolak)

### POST /api/wawancara
Schedule an interview.

**Request Body:**
```json
{
    "id_lamaran_pekerjaan": 1,
    "id_user": 2,
    "tanggal_wawancara": "2025-07-15 10:00:00",
    "lokasi": "Ruang Meeting 1",
    "catatan": "Interview untuk posisi Frontend Developer"
}
```

## 9. Selection Results Management

### GET /api/hasil-seleksi
Get selection results.

**Query Parameters:**
- `id_lowongan_pekerjaan` - Filter by job opening
- `status` - Filter by status (pending, diterima, ditolak)
- `id_user` - Filter by user (admin/HR only)

### POST /api/hasil-seleksi
Create selection result.

**Request Body:**
```json
{
    "id_user": 2,
    "id_lowongan_pekerjaan": 1,
    "status": "diterima",
    "catatan": "Kandidat memenuhi syarat dan lulus wawancara"
}
```

## 10. Training Management

### GET /api/pelatihan
Get training programs.

**Query Parameters:**
- `search` - Search by title
- `jenis_pelatihan` - Filter by training type
- `is_active` - Filter by active status
- `tanggal_dari` - Start date filter
- `tanggal_sampai` - End date filter
- `upcoming` - Show only upcoming trainings

### POST /api/pelatihan
Create a new training program.

**Request Body:**
```json
{
    "judul": "Laravel Advanced Training",
    "deskripsi": "Pelatihan Laravel tingkat lanjut",
    "jenis_pelatihan": "Technical",
    "jadwal_pelatihan": "2025-08-01 09:00:00",
    "link_url": "https://meet.google.com/example",
    "durasi": 480,
    "is_active": true
}
```

### GET /api/pelatihan/upcoming
Get upcoming trainings.

**Query Parameters:**
- `limit` - Number of trainings to return (default: 10)

### GET /api/pelatihan/statistics
Get training statistics.

**Query Parameters:**
- `year` - Year for statistics (default: current year)

### PUT /api/pelatihan/{id}/toggle-active
Toggle training active status.

## Error Responses

All endpoints return standardized error responses:

```json
{
    "status": "error",
    "message": "Error description",
    "errors": {
        "field_name": ["Validation error message"]
    }
}
```

## Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Authentication & Authorization

### User Roles:
- `admin` - Full access to all endpoints
- `front office` - Limited access to job applications and interviews
- `kasir` - Limited access to employee and salary data
- `dokter` - Access to own data only
- `beautician` - Access to own data only
- `pelanggan` - Limited access to job applications

### Role-based Access:
- Employees can only access their own attendance and salary data
- Admin and HR roles have full access to all employee data
- Job application endpoints are accessible to applicants and HR staff
- Training endpoints are accessible to all authenticated users

## Notes

1. **Simplified Attendance**: The new schema removes `jam_masuk` and `jam_keluar` from attendance, storing only the date.

2. **Automatic Salary Calculation**: The system can automatically calculate salaries based on:
   - Base salary from position
   - Bonus percentage from position
   - Attendance-based allowance

3. **File Uploads**: CV files in job applications are stored as LONGBLOB in the database (base64 encoded).

4. **Date Formats**: All dates should be in `YYYY-MM-DD` format, datetime in `YYYY-MM-DD HH:MM:SS` format.

5. **Decimal Precision**: All monetary values use decimal(12,2) for amounts and decimal(5,2) for percentages.
