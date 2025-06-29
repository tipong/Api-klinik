<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LamaranPekerjaan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_lamaran_pekerjaan';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_lamaran_pekerjaan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_lowongan_pekerjaan',
        'id_user',
        'nama_pelamar',
        'email_pelamar',
        'NIK_pelamar',
        'telepon_pelamar',
        'alamat_pelamar',
        'pendidikan_terakhir',
        'CV',
        'status_lamaran',
        'status_seleksi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'CV' => 'binary',
    ];

    /**
     * Get the lowongan pekerjaan that owns the lamaran pekerjaan.
     */
    public function lowonganPekerjaan()
    {
        return $this->belongsTo(LowonganPekerjaan::class, 'id_lowongan_pekerjaan', 'id_lowongan_pekerjaan');
    }

    /**
     * Get the user that owns the lamaran pekerjaan.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Get the wawancara for the lamaran pekerjaan.
     */
    public function wawancara()
    {
        return $this->hasMany(Wawancara::class, 'id_lamaran_pekerjaan', 'id_lamaran_pekerjaan');
    }

    /**
     * Check if the lamaran pekerjaan is pending.
     */
    public function isPending()
    {
        return $this->status_lamaran === 'pending';
    }

    /**
     * Check if the lamaran pekerjaan is accepted.
     */
    public function isAccepted()
    {
        return $this->status_lamaran === 'diterima';
    }

    /**
     * Check if the lamaran pekerjaan is rejected.
     */
    public function isRejected()
    {
        return $this->status_lamaran === 'ditolak';
    }
}
