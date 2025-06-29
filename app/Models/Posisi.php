<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posisi extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_posisi';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_posisi';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_posisi',
        'gaji_pokok',
        'persen_bonus',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'persen_bonus' => 'decimal:2',
    ];

    /**
     * Get the pegawai for the posisi.
     */
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id_posisi', 'id_posisi');
    }

    /**
     * Get the lowongan pekerjaan for the posisi.
     */
    public function lowonganPekerjaan()
    {
        return $this->hasMany(LowonganPekerjaan::class, 'id_posisi', 'id_posisi');
    }
}
