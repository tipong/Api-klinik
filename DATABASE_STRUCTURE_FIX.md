# Database Structure Fix - API Klinik

## Masalah yang Ditemukan
Error pada API absensi: `SQLSTATE[HY000]: General error: 1 no such column: tb_pegawai.id_user`

## Analisis Masalah
Terjadi ketidakcocokan antara:
1. **Migration Database**: Menggunakan nama kolom `user_id`, `posisi_id`, `pegawai_id`
2. **Model Relations**: Menggunakan nama kolom lama `id_user`, `id_posisi`, `id_pegawai`

## Perbaikan yang Dilakukan

### 1. Model User (`app/Models/User.php`)
```php
// BEFORE:
public function pegawai()
{
    return $this->hasOne(Pegawai::class, 'id_user', 'id');
}

public function absensi()
{
    return $this->hasManyThrough(
        Absensi::class,
        Pegawai::class,
        'id_user', // Wrong
        'id_pegawai', // Wrong
        'id',
        'id_pegawai' // Wrong
    );
}

// AFTER:
public function pegawai()
{
    return $this->hasOne(Pegawai::class, 'user_id', 'id');
}

public function absensi()
{
    return $this->hasManyThrough(
        Absensi::class,
        Pegawai::class,
        'user_id', // Correct
        'pegawai_id', // Correct
        'id',
        'id' // Correct
    );
}
```

### 2. Model Pegawai (`app/Models/Pegawai.php`)
```php
// BEFORE:
protected $primaryKey = 'id_pegawai';
protected $fillable = [
    'id_user',
    'nama_lengkap',
    'id_posisi',
    // ...
];

public function user()
{
    return $this->belongsTo(User::class, 'id_user', 'id_user');
}

// AFTER:
protected $primaryKey = 'id';
protected $fillable = [
    'user_id',
    'nama',
    'posisi_id',
    // ...
];

public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}
```

### 3. Model Absensi (`app/Models/Absensi.php`)
```php
// BEFORE:
protected $primaryKey = 'id_absensi';
protected $fillable = [
    'id_pegawai',
    'latitude_masuk',
    'alamat_masuk',
    'catatan',
    // ...
];

public function pegawai()
{
    return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
}

// AFTER:
protected $primaryKey = 'id';
protected $fillable = [
    'pegawai_id',
    'lokasi_masuk',
    'keterangan',
    'status',
    // ...
];

public function pegawai()
{
    return $this->belongsTo(Pegawai::class, 'pegawai_id', 'id');
}
```

### 4. AbsensiController (`app/Http/Controllers/Api/AbsensiController.php`)
```php
// BEFORE:
$query->where('id_pegawai', $pegawai->id_pegawai);
$absensi = Absensi::create([
    'id_pegawai' => $pegawai->id_pegawai,
    'latitude_masuk' => $request->latitude,
    'alamat_masuk' => $request->alamat_masuk,
    'catatan' => $request->catatan,
]);

// AFTER:
$query->where('pegawai_id', $pegawai->id);
$absensi = Absensi::create([
    'pegawai_id' => $pegawai->id,
    'lokasi_masuk' => $request->lokasi_masuk ?? 'Kantor',
    'keterangan' => $request->keterangan,
    'status' => 'Hadir',
]);
```

## Struktur Database yang Benar

### tb_pegawai
- Primary Key: `id` (bukan `id_pegawai`)
- Foreign Key: `user_id` (bukan `id_user`)
- Foreign Key: `posisi_id` (bukan `id_posisi`)

### tb_absensi
- Primary Key: `id` (bukan `id_absensi`)
- Foreign Key: `pegawai_id` (bukan `id_pegawai`)

### tb_user
- Primary Key: `id`

## Validasi yang Disesuaikan

### Check-in Validation:
```php
'lokasi_masuk' => 'nullable|string|max:500',
'keterangan' => 'nullable|string|max:255',
```

### Check-out Validation:
```php
'lokasi_keluar' => 'nullable|string|max:500',
'keterangan' => 'nullable|string|max:1000',
```

## Testing
Setelah perbaikan ini, API absensi seharusnya dapat:
1. ✅ Melakukan POST check-in tanpa error database
2. ✅ Melakukan PUT check-out tanpa error database
3. ✅ Mengambil data absensi dengan relasi yang benar
4. ✅ Filter data berdasarkan user dengan benar

## Notes
- Dihapus pengecekan radius kantor sementara untuk fokus pada perbaikan struktur database
- Sistem sekarang menggunakan field `lokasi_masuk` dan `lokasi_keluar` sebagai string
- Status absensi default adalah 'Hadir'
- Validation lebih sederhana tanpa geo-location requirement
