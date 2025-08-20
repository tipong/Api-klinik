# SQLite Migration Guide - Api-klinik

## Perubahan Database ke SQLite

Proyek Api-klinik telah berhasil diupdate untuk menggunakan **SQLite** sebagai database utama, menggantikan MySQL/MariaDB.

### Perubahan yang Dilakukan

#### 1. **Konfigurasi Database (.env)**
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

#### 2. **Migrasi Database**
Semua migrasi telah diupdate untuk kompatibilitas SQLite:

- **ENUM → STRING**: SQLite tidak mendukung ENUM, diganti dengan STRING
- **Check Constraints**: Dihapus karena Laravel Blueprint tidak mendukung di versi ini
- **Foreign Keys**: Tetap berfungsi normal
- **Timestamps**: Diupdate untuk kompatibilitas SQLite

#### 3. **Tabel yang Diupdate**

| Tabel | Perubahan Utama |
|-------|-----------------|
| `tb_user` | `role` ENUM → STRING(50) |
| `tb_pegawai` | `jenis_kelamin` ENUM → STRING(20) |
| `tb_absensi` | `status` ENUM → STRING(20) |
| `tb_gaji` | `status` ENUM → STRING(20) |
| `tb_lowongan_pekerjaan` | `status` ENUM → STRING(20) |
| `tb_lamaran_pekerjaan` | `status_lamaran` ENUM → STRING(20) |
| `tb_wawancara` | `status` (langsung STRING, bukan `hasil`) |
| `tb_hasil_seleksi` | `status` ENUM → STRING(20), FK ke `lamaran` |

#### 4. **Seeder Updates**
- **BaseSeeder**: Diupdate untuk mendukung SQLite foreign key handling
- Menggunakan `PRAGMA foreign_keys=OFF/ON` untuk SQLite
- Fallback ke MySQL syntax jika diperlukan

### Migrasi yang Diperbaiki

#### **Migrasi Utama**
- `2025_07_01_000001_recreate_tb_user_table.php`
- `2025_07_01_000003_recreate_tb_pegawai_table.php`
- `2025_07_12_000002_recreate_simple_tb_absensi_table.php`
- `2025_07_01_000005_recreate_tb_gaji_table.php`
- `2025_07_01_000006_recreate_tb_lowongan_pekerjaan_table.php`
- `2025_07_01_000007_recreate_tb_lamaran_pekerjaan_table.php`
- `2025_07_01_000008_recreate_tb_wawancara_table.php`
- `2025_07_01_000009_recreate_tb_hasil_seleksi_table.php`

#### **Migrasi Update (No-Op untuk SQLite)**
- `2025_07_22_073106_update_tb_hasil_seleksi_change_lowongan_to_lamaran.php`
- `2025_07_22_073133_update_tb_wawancara_change_hasil_to_status.php`
- `2025_07_22_192228_update_tb_wawancara_status_enum_values.php`

### Cara Menjalankan

#### **Fresh Migration dengan Seeder**
```bash
cd "/Users/macbook/Documents/Coding/Frontend TA/Api-klinik"
php artisan migrate:fresh --seed
```

#### **Start API Server**
```bash
php artisan serve --port=8001
```

#### **Test Database**
```bash
php artisan tinker --execute="echo 'Users: ' . App\Models\User::count();"
```

### Verifikasi Data

Setelah migrasi berhasil, data berikut tersedia:
- **Users**: 10 (termasuk admin, hrd, dll)
- **Posisi**: 6 (Admin, Dokter, Beautician, dll)
- **Lowongan**: 5 lowongan aktif
- **Lamaran**: 6 lamaran pekerjaan
- **Data Treatment, Booking, Absensi, Gaji**: Lengkap

### Kompatibilitas

✅ **Model Eloquent**: Tetap sama, tidak perlu perubahan
✅ **API Endpoints**: Semua endpoint tetap berfungsi
✅ **Relationships**: Foreign keys tetap berfungsi
✅ **Validation**: Sesuaikan dengan field STRING, bukan ENUM
✅ **Seeder**: Diupdate untuk SQLite compatibility

### Perintah Berguna

```bash
# Cek status migrasi
php artisan migrate:status

# Rollback jika diperlukan
php artisan migrate:rollback

# Clear cache
php artisan config:clear
php artisan cache:clear

# Check database
php artisan tinker
>>> DB::select('SELECT name FROM sqlite_master WHERE type="table"');
```

### Keuntungan SQLite

1. **Portable**: File database tunggal, mudah backup/transfer
2. **No Server**: Tidak perlu MySQL server terpisah
3. **Fast**: Performa cepat untuk development dan testing
4. **Simple**: Setup dan maintenance lebih mudah
5. **Cross-platform**: Berjalan di semua OS

### Notes

- File database: `database/database.sqlite`
- Size: ~50KB (dengan sample data)
- Backup: Cukup copy file `.sqlite`
- Development-friendly: Perfect untuk development dan testing

---

**Status**: ✅ **COMPLETED**
**Database**: SQLite
**Migration**: SUCCESS
**Seeder**: SUCCESS
**API Server**: Running on port 8001
