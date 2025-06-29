# API Klinik Management System

API backend untuk sistem manajemen klinik dengan fitur lengkap untuk mengelola absensi, rekrutmen, pelatihan, penggajian, dan manajemen pegawai.

## 🚀 Fitur Utama

### ✅ Sistem Absensi GPS
- Check-in/check-out dengan validasi lokasi GPS
- Tracking radius kantor (default 100 meter)
- Perhitungan keterlambatan dan lembur otomatis
- Approval absensi oleh HRD

### 🏢 Manajemen Rekrutmen
- Posting lowongan kerja
- Sistem aplikasi lamaran online
- Tracking status lamaran (pending, review, interview, diterima, ditolak)
- Upload CV dan dokumen pendukung

### 📚 Sistem Pelatihan
- Manajemen pelatihan internal/eksternal
- Pendaftaran peserta pelatihan
- Evaluasi dan sertifikasi
- Tracking progress pelatihan

### 💰 Penggajian Otomatis
- Perhitungan gaji berdasarkan absensi
- Komponen: gaji pokok, tunjangan, bonus, lembur
- Potongan: keterlambatan, alpha, BPJS, pajak
- Slip gaji digital

### 👥 Manajemen Pegawai
- Data lengkap pegawai
- Struktur organisasi
- Kontrak kerja dan status pegawai

## 🔐 Role-based Access Control

### Admin
- ✅ Akses penuh semua fitur
- ❌ Tidak perlu absen

### HRD
- ✅ Akses penuh semua fitur
- ✅ Wajib absen

### Staff (Beautician, Dokter, Front Office, Kasir)
- ✅ Absensi
- ✅ Lihat pelatihan
- ✅ Lihat slip gaji
- ❌ Manajemen data

### Pelanggan
- ✅ Daftar lowongan kerja saja

## 🛠️ Tech Stack

- **Framework**: Laravel 11
- **Authentication**: Laravel Sanctum
- **Database**: SQLite (development)
- **GPS**: Haversine formula untuk validasi lokasi
- **API Format**: RESTful JSON API

## 📦 Installation

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM (optional, untuk frontend)

### Setup Steps

1. **Clone Repository**
```bash
git clone <repository-url>
cd Api-klinik
```

2. **Install Dependencies**
```bash
composer install
```

3. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Setup**
```bash
php artisan migrate:fresh --seed
```

5. **Start Development Server**
```bash
php artisan serve
```

API akan berjalan di `http://127.0.0.1:8000`

## 🗄️ Database Schema

### Users & Authentication
- `users` - Data pengguna dengan role-based access
- `personal_access_tokens` - Laravel Sanctum tokens

### Employee Management
- `employees` - Data pegawai lengkap
- `attendances` - Data absensi dengan GPS tracking

### Recruitment
- `recruitments` - Lowongan kerja
- `applications` - Lamaran pekerjaan

### Training
- `trainings` - Data pelatihan
- `training_participants` - Peserta pelatihan

### Payroll
- `salaries` - Data gaji dan perhitungan

## 🔑 Default Users

Setelah menjalankan seeder, tersedia user default:

| Role | Email | Password | Keterangan |
|------|-------|----------|------------|
| Admin | admin@klinik.com | password123 | Full access, tidak perlu absen |
| HRD | hrd@klinik.com | password123 | Full access, wajib absen |
| Beautician | beautician@klinik.com | password123 | Limited access, wajib absen |
| Dokter | dokter@klinik.com | password123 | Limited access, wajib absen |
| Front Office | frontoffice@klinik.com | password123 | Limited access, wajib absen |
| Kasir | kasir@klinik.com | password123 | Limited access, wajib absen |
| Pelanggan | pelanggan@test.com | password123 | Job application only |

## 📡 API Endpoints

### Authentication
```
POST /api/auth/register     - Register user baru
POST /api/auth/login        - Login
GET  /api/auth/profile      - Get user profile
PUT  /api/auth/profile      - Update profile
POST /api/auth/logout       - Logout
POST /api/auth/logout-all   - Logout from all devices
```

### Dashboard
```
GET  /api/dashboard         - Dashboard data (Admin/HRD only)
```

### Health Check
```
GET  /api/health           - API health status
```

## ⚙️ Configuration

### Office Location (GPS)
Edit file `.env`:
```env
OFFICE_LATITUDE=-6.2088
OFFICE_LONGITUDE=106.8456
OFFICE_RADIUS=100
```

### Work Schedule
```env
WORK_START_TIME=08:00
WORK_END_TIME=17:00
LATE_TOLERANCE_MINUTES=15
```

### Salary Calculation
```env
ATTENDANCE_ALLOWANCE_BASE=200000
LATE_PENALTY_PER_DAY=10000
OVERTIME_MULTIPLIER=1.5
```

## 🔧 VS Code Tasks

Tersedia VS Code tasks untuk development:
- `Start Laravel API Server` - Jalankan server development
- `Run Migrations` - Jalankan migrasi database
- `Run Migrations Fresh with Seed` - Reset database dengan data seed
- `Clear Application Cache` - Clear cache aplikasi
- Dan lainnya...

Akses melalui: `Ctrl/Cmd + Shift + P` → `Tasks: Run Task`

## 🧪 Testing

```bash
php artisan test
```

## 📝 API Documentation

Generate dokumentasi API:
```bash
php artisan route:list --columns=method,uri,name,action
```

## 📮 Postman Testing

### Import Collection
1. **Collection**: Import `postman/Klinik-API-Collection.postman_collection.json`
2. **Environment**: Import `postman/Klinik-API-Local.postman_environment.json`
3. **Select Environment**: "Klinik API - Local"

### Test Coverage
- ✅ **23 endpoints** untuk testing komprehensif
- ✅ **Authentication** - Register, Login semua role, Profile management
- ✅ **Authorization** - Role-based access control testing
- ✅ **Error Handling** - Validation dan security testing
- ✅ **Auto Token Management** - Token tersimpan otomatis setelah login

### Quick Test
```bash
# Health Check
curl http://127.0.0.1:8000/api/health

# Login Admin
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@klinik.com", "password": "password123"}'
```

**Dokumentasi lengkap**: `postman/API-Testing-Guide.md`

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License.

## 📞 Support

Untuk bantuan dan pertanyaan:
- Email: support@klinik.com
- Documentation: [Link to docs]
- Issues: [GitHub Issues]
