<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_pegawai';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_pegawai';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user',
        'nama_lengkap',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'telepon',
        'email',
        'NIP',
        'NIK',
        'id_posisi',
        'agama',
        'tanggal_masuk',
        'tanggal_keluar',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    /**
     * Get the user that owns the pegawai.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Get the posisi that owns the pegawai.
     */
    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'id_posisi', 'id_posisi');
    }

    /**
     * Get the absensi for the pegawai.
     */
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'id_pegawai', 'id_pegawai');
    }

    /**
     * Get the gaji for the pegawai.
     */
    public function gaji()
    {
        return $this->hasMany(Gaji::class, 'id_pegawai', 'id_pegawai');
    }

    /**
     * Check if pegawai is active (has not left yet).
     */
    public function isActive()
    {
        return is_null($this->tanggal_keluar) || $this->tanggal_keluar->isFuture();
    }

    /**
     * Get full name.
     */
    public function getFullNameAttribute()
    {
        return $this->nama_lengkap;
    }

    /**
     * Get employment duration.
     */
    public function getEmploymentDurationAttribute()
    {
        $endDate = $this->tanggal_keluar ?? now();
        return $this->tanggal_masuk->diffInDays($endDate);
    }
}
