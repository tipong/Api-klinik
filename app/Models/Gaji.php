<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_gaji';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_gaji';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pegawai',
        'periode_bulan',
        'periode_tahun',
        'gaji_pokok',
        'gaji_bonus',
        'gaji_kehadiran',
        'gaji_total',
        'tanggal_pembayaran',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'periode_bulan' => 'integer',
        'periode_tahun' => 'integer',
        'gaji_pokok' => 'decimal:2',
        'gaji_bonus' => 'decimal:2',
        'gaji_kehadiran' => 'decimal:2',
        'gaji_total' => 'decimal:2',
        'tanggal_pembayaran' => 'date',
    ];

    /**
     * Get the pegawai that owns the gaji.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    /**
     * Get the formatted periode.
     */
    public function getPeriodeFormattedAttribute()
    {
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return $bulan[$this->periode_bulan - 1] . ' ' . $this->periode_tahun;
    }
}
