# Klinik HRD Management API - Testing Guide

## Overview
API ini adalah sistem manajemen HRD untuk klinik yang mencakup:
- Authentication & Authorization
- Absensi Management 
- Pegawai Management
- Posisi Management
- Gaji Management
- Recruitment Management
- Multi-role Dashboard

## Base URL
```
http://localhost:8000/api
```

## Authentication
Semua endpoint yang dilindungi memerlukan Bearer Token yang didapat dari login.

### Header untuk request yang memerlukan auth:
```
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

---

## 1. AUTHENTICATION ENDPOINTS

### 1.1 Register User
```
POST /auth/register
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john.doe@example.com", 
    "password": "password123",
    "password_confirmation": "password123",
    "role": "pegawai"
}
```

**Roles Available:**
- `admin` - Full access
- `hrd` - HR management access
- `pegawai` - Basic employee access
- `front_office` - Front desk operations
- `kasir` - Cashier operations
- `dokter` - Doctor operations
- `beautician` - Beauty treatment operations

### 1.2 Login
```
POST /auth/login
```

**Request Body:**
```json
{
    "email": "admin@example.com",
    "password": "admin123"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Login successful",
    "data": {
        "user": {
            "id_user": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "role": "admin"
        },
        "token": "1|abc123def456..."
    }
}
```

### 1.3 Get Profile
```
GET /auth/profile
Authorization: Bearer {token}
```

### 1.4 Update Profile
```
PUT /auth/profile
Authorization: Bearer {token}

{
    "name": "Updated Name",
    "email": "updated@example.com"
}
```

### 1.5 Logout
```
POST /auth/logout
Authorization: Bearer {token}
```

---

## 2. ABSENSI MANAGEMENT

### 2.1 Check-in (Absen Masuk)
```
POST /absensi
Authorization: Bearer {token}

{
    "status": "Hadir"
}
```

**Status Options:** `Hadir`, `Sakit`, `Izin`, `Alpa`

### 2.2 Check-out (Absen Keluar)
```
POST /absensi/{id}/checkout
Authorization: Bearer {token}

{}
```

### 2.3 Status Absensi Hari Ini
```
GET /absensi/today-status
Authorization: Bearer {token}
```

### 2.4 Daftar Absensi
```
GET /absensi
Authorization: Bearer {token}
```

**Query Parameters:**
- `tanggal` - Filter by date (YYYY-MM-DD)
- `bulan` - Filter by month (1-12)
- `tahun` - Filter by year
- `status` - Filter by status

### 2.5 Detail Absensi
```
GET /absensi/{id}
Authorization: Bearer {token}
```

---

## 3. PEGAWAI MANAGEMENT (Admin/HRD Only)

### 3.1 Daftar Pegawai
```
GET /pegawai
Authorization: Bearer {token}
```

### 3.2 Tambah Pegawai
```
POST /pegawai
Authorization: Bearer {token}

{
    "id_user": 1,
    "id_posisi": 1,
    "nomor_ktp": "1234567890123456",
    "alamat": "Jl. Contoh No. 123",
    "no_telepon": "081234567890",
    "tanggal_bergabung": "2025-01-01"
}
```

### 3.3 Detail Pegawai
```
GET /pegawai/{id}
Authorization: Bearer {token}
```

### 3.4 Update Pegawai
```
PUT /pegawai/{id}
Authorization: Bearer {token}

{
    "alamat": "Jl. Updated No. 456",
    "no_telepon": "081234567899"
}
```

### 3.5 Hapus Pegawai
```
DELETE /pegawai/{id}
Authorization: Bearer {token}
```

---

## 4. POSISI MANAGEMENT (Admin/HRD Only)

### 4.1 Daftar Posisi
```
GET /posisi
Authorization: Bearer {token}
```

### 4.2 Tambah Posisi
```
POST /posisi
Authorization: Bearer {token}

{
    "nama_posisi": "Front Office",
    "gaji_pokok": 3000000,
    "tunjangan": 500000,
    "deskripsi": "Melayani pelanggan dan administrasi"
}
```

### 4.3 Detail Posisi
```
GET /posisi/{id}
Authorization: Bearer {token}
```

### 4.4 Update Posisi
```
PUT /posisi/{id}
Authorization: Bearer {token}
```

### 4.5 Hapus Posisi
```
DELETE /posisi/{id}
Authorization: Bearer {token}
```

---

## 5. GAJI MANAGEMENT (Admin/HRD Only)

### 5.1 Daftar Gaji
```
GET /gaji
Authorization: Bearer {token}
```

### 5.2 Generate Gaji
```
POST /gaji/generate
Authorization: Bearer {token}

{
    "bulan": 7,
    "tahun": 2025,
    "id_pegawai": [1, 2, 3]
}
```

### 5.3 Preview Kalkulasi Gaji
```
GET /gaji/preview?bulan=7&tahun=2025&id_pegawai=1
Authorization: Bearer {token}
```

---

## 6. RECRUITMENT MANAGEMENT

### 6.1 Daftar Lowongan (Public)
```
GET /lowongan
```

### 6.2 Detail Lowongan (Public)
```
GET /lowongan/{id}
```

### 6.3 Apply Lowongan (Public)
```
POST /lowongan/apply

{
    "id_lowongan": 1,
    "nama_pelamar": "Jane Doe",
    "email_pelamar": "jane.doe@example.com",
    "no_telepon_pelamar": "081234567890",
    "alamat_pelamar": "Jl. Pelamar No. 123"
}
```

### 6.4 Tambah Lowongan (Admin/HRD Only)
```
POST /lowongan-pekerjaan
Authorization: Bearer {token}

{
    "id_posisi": 1,
    "judul_lowongan": "Receptionist",
    "deskripsi_pekerjaan": "Melayani tamu dan telepon",
    "persyaratan": "Minimal SMA, komunikatif",
    "gaji_ditawarkan": 3500000,
    "tanggal_posting": "2025-07-12",
    "tanggal_penutupan": "2025-08-12",
    "status": "aktif"
}
```

---

## 7. STAFF DASHBOARDS

### 7.1 Dashboard Admin/HRD
```
GET /dashboard
Authorization: Bearer {token}
```

### 7.2 Dashboard Front Office
```
GET /front-office/dashboard
Authorization: Bearer {token}
```

### 7.3 Dashboard Kasir
```
GET /kasir/dashboard
Authorization: Bearer {token}
```

### 7.4 Dashboard Dokter
```
GET /dokter/dashboard
Authorization: Bearer {token}
```

### 7.5 Dashboard Beautician
```
GET /beautician/dashboard
Authorization: Bearer {token}
```

---

## 8. SYSTEM UTILITIES

### 8.1 Health Check
```
GET /health
```

### 8.2 Get User Info
```
GET /user
Authorization: Bearer {token}
```

---

## TESTING SCENARIOS

### Scenario 1: Admin Complete Flow
1. **Login as Admin** → Get admin token
2. **Create Position** → Add new job position
3. **Register User** → Create new user account
4. **Add Employee** → Link user to employee record
5. **Generate Salary** → Calculate monthly salary
6. **View Dashboard** → Check admin dashboard

### Scenario 2: Employee Daily Flow
1. **Login as Employee** → Get employee token
2. **Check Today Status** → Verify attendance status
3. **Check-in** → Mark attendance entry
4. **Check Today Status** → Verify check-in recorded
5. **Check-out** → Mark attendance exit
6. **View Attendance History** → Check attendance records

### Scenario 3: Recruitment Flow
1. **View Job Openings** → Public access to jobs
2. **Apply for Job** → Submit application
3. **Login as HRD** → Access HR dashboard
4. **Review Applications** → Check submitted applications
5. **Schedule Interview** → Set interview appointments
6. **Process Selection** → Record selection results

### Scenario 4: Multi-Role Dashboard Testing
1. **Login as Front Office** → Access front office dashboard
2. **Login as Kasir** → Access cashier dashboard  
3. **Login as Dokter** → Access doctor dashboard
4. **Login as Beautician** → Access beautician dashboard

---

## ERROR HANDLING

### Common HTTP Status Codes:
- **200**: Success (GET requests)
- **201**: Created (POST requests)
- **400**: Bad Request (validation errors)
- **401**: Unauthorized (invalid/missing token)
- **403**: Forbidden (insufficient permissions)
- **404**: Not Found (resource not found)
- **422**: Validation Error (form validation failed)

### Sample Error Response:
```json
{
    "status": "error",
    "message": "Validation error",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

---

## POSTMAN SETUP

### 1. Import Files:
- Collection: `Klinik_HRD_Complete_API.postman_collection.json`
- Environment: `Klinik_HRD_Complete_Environment.postman_environment.json`

### 2. Environment Variables:
- `base_url`: http://localhost:8000/api
- `token`: Auto-set from login response
- Various user credentials for different roles

### 3. Auto Token Management:
Login request automatically saves token to environment variable for subsequent requests.

---

## ROLE-BASED ACCESS CONTROL

### Admin/HRD Access:
- Full CRUD operations on all resources
- User management
- Employee management
- Salary calculation
- Recruitment management

### Employee Access:
- Own attendance management
- Own profile management
- View own salary records

### Staff Access (Front Office, Kasir, Dokter, Beautician):
- Role-specific dashboard access
- Basic attendance functionality
- Profile management

### Public Access:
- View job openings
- Submit job applications
- Health check endpoint
