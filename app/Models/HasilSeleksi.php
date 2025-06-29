<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilSeleksi extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_hasil_seleksi';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_hasil_seleksi';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user',
        'id_lowongan_pekerjaan',
        'status',
        'catatan',
    ];

    /**
     * Get the user that owns the hasil seleksi.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Get the lowongan pekerjaan that owns the hasil seleksi.
     */
    public function lowonganPekerjaan()
    {
        return $this->belongsTo(LowonganPekerjaan::class, 'id_lowongan_pekerjaan', 'id_lowongan_pekerjaan');
    }

    /**
     * Check if the hasil seleksi is accepted.
     */
    public function isAccepted()
    {
        return $this->status === 'diterima';
    }

    /**
     * Check if the hasil seleksi is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'ditolak';
    }

    /**
     * Check if the hasil seleksi is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}
