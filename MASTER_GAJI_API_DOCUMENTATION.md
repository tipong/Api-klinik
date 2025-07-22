# API Master Gaji Pegawai Documentation

## Overview

API Master Gaji Pegawai memungkinkan pengelolaan gaji pokok tambahan untuk pegawai secara individual. Sistem ini memberikan fleksibilitas untuk memberikan gaji pokok yang berbeda dari standar posisi.

## Authentication

Semua endpoint memerlukan Bearer Token Authentication:
```
Authorization: Bearer {your_token}
```

## Endpoints

### 1. Get All Master Gaji Pegawai

**URL:** `GET /api/master-gaji`

**Description:** Mengambil daftar semua pegawai dengan informasi gaji pokok mereka.

**Query Parameters:**
- `nama` (optional): Filter berdasarkan nama pegawai
- `id_posisi` (optional): Filter berdasarkan ID posisi
- `status` (optional): Filter berdasarkan status pegawai (aktif/non_aktif)
- `per_page` (optional): Jumlah data per halaman (default: 15)

**Response Example:**
```json
{
  "status": "success",
  "message": "Data master gaji pegawai berhasil diambil",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id_pegawai": 1,
        "nama_lengkap": "Ahmad Supardi",
        "NIP": "ADM001",
        "posisi": {
          "id_posisi": 6,
          "nama_posisi": "Admin",
          "gaji_pokok_default": "7000000.00"
        },
        "gaji_pokok_tambahan": "15000000.00",
        "gaji_pokok_efektif": "15000000.00",
        "has_custom_salary": true,
        "status_pegawai": "aktif",
        "tanggal_masuk": "2024-01-01",
        "tanggal_keluar": null
      }
    ],
    "pagination_info": "..."
  }
}
```

### 2. Get Specific Master Gaji Pegawai

**URL:** `GET /api/master-gaji/{id}`

**Description:** Mengambil informasi gaji pokok pegawai tertentu.

**Response Example:**
```json
{
  "status": "success",
  "message": "Data master gaji pegawai berhasil diambil",
  "data": {
    "id_pegawai": 1,
    "nama_lengkap": "Ahmad Supardi",
    "NIP": "ADM001",
    "email": "admin@klinik.com",
    "telepon": "081234567890",
    "posisi": {
      "id_posisi": 6,
      "nama_posisi": "Admin",
      "gaji_pokok_default": "7000000.00"
    },
    "gaji_pokok_tambahan": "15000000.00",
    "gaji_pokok_efektif": "15000000.00",
    "has_custom_salary": true,
    "status_pegawai": "aktif",
    "tanggal_masuk": "2024-01-01",
    "tanggal_keluar": null
  }
}
```

### 3. Update Master Gaji Pegawai

**URL:** `PUT /api/master-gaji/{id}`

**Description:** Mengupdate gaji pokok tambahan pegawai.

**Request Body:**
```json
{
  "gaji_pokok_tambahan": 15000000
}
```

**Response Example:**
```json
{
  "status": "success",
  "message": "Gaji pokok tambahan pegawai berhasil diperbarui",
  "data": {
    "id_pegawai": 1,
    "nama_lengkap": "Ahmad Supardi",
    "gaji_pokok_tambahan": "15000000.00",
    "gaji_pokok_efektif": "15000000.00",
    "has_custom_salary": true,
    "posisi": {
      "nama_posisi": "Admin",
      "gaji_pokok_default": "7000000.00"
    }
  }
}
```

### 4. Reset Custom Salary

**URL:** `POST /api/master-gaji/{id}/reset`

**Description:** Mereset gaji pokok tambahan pegawai ke 0 (menggunakan default posisi).

**Response Example:**
```json
{
  "status": "success",
  "message": "Gaji pokok tambahan pegawai berhasil direset ke default posisi",
  "data": {
    "id_pegawai": 1,
    "nama_lengkap": "Ahmad Supardi",
    "gaji_pokok_tambahan": "0.00",
    "gaji_pokok_efektif": "7000000.00",
    "has_custom_salary": false
  }
}
```

## Logika Gaji Pokok Efektif

Sistem menggunakan logika prioritas sebagai berikut:

1. **Jika `gaji_pokok_tambahan` > 0**: Gunakan nilai ini sebagai gaji pokok
2. **Jika `gaji_pokok_tambahan` = 0 atau NULL**: Gunakan gaji pokok dari tabel posisi

## Generate Gaji (Updated Logic)

**URL:** `POST /api/gaji/generate`

**Description:** Generate gaji bulanan untuk semua pegawai dengan logika yang diperbarui.

**Request Body:**
```json
{
  "periode_bulan": 7,
  "periode_tahun": 2025
}
```

**Calculation Logic:**
1. **Gaji Pokok**: Menggunakan `getGajiPokokEfektif()` method dari model Pegawai
2. **Gaji Absensi**: Jumlah absensi dengan status "Hadir" × 100,000
3. **Gaji Bonus**: Total harga booking treatment yang diselesaikan × persentase bonus posisi

**Formula:**
```
Gaji Total = Gaji Pokok Efektif + (Jumlah Hadir × 100,000) + (Total Booking × Persen Bonus)
```

## Error Responses

**Validation Error (422):**
```json
{
  "status": "error",
  "message": "Validation error",
  "errors": {
    "gaji_pokok_tambahan": ["The gaji pokok tambahan field is required."]
  }
}
```

**Not Found (404):**
```json
{
  "status": "error",
  "message": "Pegawai tidak ditemukan"
}
```

**Server Error (500):**
```json
{
  "status": "error",
  "message": "Gagal memperbarui gaji pokok tambahan pegawai"
}
```

## Notes

- API hanya dapat diakses oleh user dengan role `admin` atau `hrd`
- Gaji pokok tambahan disimpan dalam format decimal(15,2)
- Nilai 0 pada gaji_pokok_tambahan berarti menggunakan default dari posisi
- Sistem akan otomatis menggunakan gaji pokok efektif saat generate gaji
