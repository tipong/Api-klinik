<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LowonganPekerjaan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_lowongan_pekerjaan';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_lowongan_pekerjaan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'judul_pekerjaan',
        'id_posisi',
        'jumlah_lowongan',
        'pengalaman_minimal',
        'gaji_minimal',
        'gaji_maksimal',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'persyaratan',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jumlah_lowongan' => 'integer',
        'gaji_minimal' => 'decimal:2',
        'gaji_maksimal' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Get the posisi that owns the lowongan pekerjaan.
     */
    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'id_posisi', 'id_posisi');
    }

    /**
     * Get the lamaran pekerjaan for the lowongan pekerjaan.
     */
    public function lamaranPekerjaan()
    {
        return $this->hasMany(LamaranPekerjaan::class, 'id_lowongan_pekerjaan', 'id_lowongan_pekerjaan');
    }

    /**
     * Get the hasil seleksi for the lowongan pekerjaan.
     */
    public function hasilSeleksi()
    {
        return $this->hasMany(HasilSeleksi::class, 'id_lowongan_pekerjaan', 'id_lowongan_pekerjaan');
    }

    /**
     * Check if the lowongan pekerjaan is active.
     */
    public function isActive()
    {
        return $this->status === 'aktif';
    }

    /**
     * Check if the lowongan pekerjaan is expired.
     */
    public function isExpired()
    {
        return now()->gt($this->tanggal_selesai);
    }
}
